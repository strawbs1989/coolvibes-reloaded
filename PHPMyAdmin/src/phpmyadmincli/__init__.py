#!/usr/bin/env python

import os
import re
import sys
import csv
import time
import requests
from getpass import getpass
from optparse import OptionParser
from prettytable import PrettyTable

def main():
	class QueryException(Exception):
		pass

	try:
		version = 'phpmyadmin-cli  Ver 1.0 Released 1 April 2014'
		python2 = sys.version_info[0] < 3

		def usage():
			return version + """

Copyright (c) 2014, fdev.nl. All rights reserved.

This application is not affiliated with or endorsed
by the phpMyAdmin Project or its trademark owners.

Usage: phpmyadmin-cli [OPTIONS] database
  -e, --execute=name  Execute command and quit.
  -E, --export=table  Export specified tables, can be used multiple times.
  -A, --export-all    Export all tables.
  -h, --help          Display this help and exit.
  -l, --location=url  Location of phpMyAdmin (http://localhost/phpmyadmin/).
  -p                  Prompt for password to use.
  --password=name     Password to use.
  -s, --ssl-ignore    Ignore bad SSL certificates.
  -t, --timeout=n     Http request timeout in seconds.
  -u, --user=name     User for login if not current user.
  -V, --version       Output version information and exit.
"""

		parser = OptionParser(usage='%prog [OPTIONS] database')
		parser.format_help = usage
		parser.add_option('-e', '--execute', action='store', dest='execute', default='')
		parser.add_option('-E', '--export', action='append', dest='export', default=[])
		parser.add_option('-A', '--export-all', action='store_true', dest='export_all', default=False)
		parser.add_option('-l', '--location', action='store', dest='location', default='http://localhost/phpmyadmin/')
		parser.add_option('-p', action='store_true', dest='askpass', default=False)
		parser.add_option('--password', action='store', dest='password', default='')
		parser.add_option('-s', '--ssl-ignore', action='store_false', dest='verify', default=True)
		parser.add_option('-t', '--timeout', action='store', type='int', dest='timeout', default=None)
		parser.add_option('-u', '--user', action='store', dest='user', default=False)
		parser.add_option('-V', '--version', action='store_true', dest='version', default=False)

		options, args = parser.parse_args()
		kwargs = dict([(k, v) for k, v in options.__dict__.items()])

		if kwargs.get('version'):
			print(version)
			sys.exit()

		if len(args) != 1:
			parser.print_usage()
			sys.exit()

		database = args[0]
		phpmyadmin = kwargs.get('location').rstrip('/') + '/'
		user = kwargs.get('user')
		execute = kwargs.get('execute')
		export = kwargs.get('export')
		export_all = kwargs.get('export_all')
		askpass = kwargs.get('askpass')
		password = kwargs.get('password')
		verify = kwargs.get('verify')
		timeout = kwargs.get('timeout')

		if user is False:
			user = os.environ.get('USER', 'root')

		if askpass:
			password = getpass('Enter password: ')

		# Open phpMyAdmin
		session = requests.Session()
		result = session.get(phpmyadmin, verify=verify, timeout=timeout)
		if result.status_code not in (200, 401):
			sys.exit('Could not connect to phpMyAdmin.')

		# Auth type is http
		if result.status_code == 401:
			session.auth = (user, password)
			result = session.get(phpmyadmin, verify=verify, timeout=timeout)

			if result.status_code == 401:
				sys.exit("ERROR #0401: Access denied for user '%s'@'%s' (using password: %s)" % (user, phpmyadmin, 'YES' if password else 'NO'))

		# Auth type is cookie or config
		else:
			# Auth type is cookie
			if re.search(r'name="login_form"', result.text):
				# Look for token
				match = re.search(r'<input type="hidden" name="token" value="([a-f0-9]{32})"', result.text)
				if not match:
					sys.exit('Unsupported version of phpMyAdmin.')
				token = match.group(1)

				# Perform login
				data = {
					'pma_username' : user,
					'pma_password' : password,
					'server' : 1,
					'token' : token,
				}
				result = session.post(phpmyadmin, data=data, verify=verify, timeout=timeout)

				# Still at the login screen
				match = re.search(r'name="login_form"', result.text)
				if match:
					sys.exit("ERROR #0401: Access denied for user '%s'@'%s' (using password: %s)" % (user, phpmyadmin, 'YES' if password else 'NO'))

		# Search for token
		match_3x = re.search(r"var token = '([a-f0-9]{32})';", result.text)
		match_4x = re.search(r'token:"([a-f0-9]{32})"', result.text)
		if match_3x:
			token = match_3x.group(1)
		elif match_4x:
			token = match_4x.group(1)
		else:
			sys.exit('Unsupported version of phpMyAdmin.')

		def query_import(q):
			"""Perform one or more queries, separated by semi-colon."""
			data = {
				'db' : database,
				'table' : 'something',
				'token' : token,
				'sql_query' : q,
			}

			result = session.post(phpmyadmin + 'import.php', data=data, verify=verify, timeout=timeout)
			match = re.search(r'<div class="(notice|error)">(#\d+.*?)</div>', result.text, re.DOTALL)
			if match:
				raise QueryException(match.group(2))

		def query_export(tables):
			"""Perform one or more queries, separated by semi-colon."""
			data = {
				'db' : database,
				'token' : token,
				'export_type' : 'database',
				'export_method' : 'quick',
				'quick_or_custom' : 'quick',
				'table_select[]' : tables,
				'table_structure[]' : tables,
				'table_data[]' : tables,
				'output_format' : 'sendit',
				'filename_template' : '@DATABASE@',
				'remember_template' : 'on',
				'charset_of_file' : 'utf-8',
				'compression' : 'none',
				'maxsize' : '',
				'what' : 'sql',
				'sql_include_comments' : 'something',
				'sql_header_comment' : '',
				'sql_compatibility' : 'NONE',
				'sql_structure_or_data' : 'structure_and_data',
				'sql_create_table' : 'something',
				'sql_create_view' : 'something',
				'sql_procedure_function' : 'something',
				'sql_create_trigger' : 'something',
				'sql_create_table_statements' : 'something',
				'sql_if_not_exists' : 'something',
				'sql_auto_increment' : 'something',
				'sql_backquotes' : 'something',
				'sql_type' : 'INSERT',
				'sql_insert_syntax' : 'both',
				'sql_max_query_size' : '50000',
				'sql_hex_for_binary' : 'something',
				'sql_utc_time' : 'something'
			}

			result = session.post(phpmyadmin + 'export.php', data=data, verify=verify, timeout=timeout)
			result.encoding = 'utf-8'
			if not result:
				raise QueryException("Unable to export database.")
			return result.text.encode('utf8') if python2 else result.text

		def query(q):
			"""Perform a single query and return the returned rows."""
			data = {
				'db' : database,
				'table' : 'article',
				'token' : token,
				'sql_query' : q,
				'single_table' : 'TRUE',
				'export_type' : 'table',
				'allrows' : '1',
				'charset_of_file' : 'utf-8',
				'compression' : 'none',
				'what' : 'csv',
				'csv_separator' : ',',
				'csv_enclosed' : '"',
				'csv_escaped' : '"',
				'csv_terminated' : 'AUTO',
				'csv_null' : 'NULL',
				'csv_columns' : 'something',
				'csv_structure_or_data' : 'data',
				'csv_data' : '',
				# 3.x specific
				'asfile' : 'sendit',
				# 4.x specific
				'output_format' : 'sendit',
			}

			result = session.post(phpmyadmin + 'export.php', data=data, verify=verify, timeout=timeout)
			result.encoding = 'utf-8'
			if result and 'text/comma-separated-values' in result.headers['content-type']:
				# Detect invalid query
				if not result.text.strip().startswith('<!-- PMA-SQL-ERROR -->') and not result.text.startswith('<div class="error">'):
					text = result.text.strip()
					return text.encode('utf8') if python2 else text

			if re.search(r'name="login_form"', result.text):
				sys.exit('ERROR #0104: Session with phpMyAdmin expired.')

			match = re.search(r'<code>\s*(.*?)\s*</code>', result.text, re.DOTALL)
			raise QueryException(match.group(1) if match else 'Could not execute query.')

		# Perform export
		if export or export_all:
			try:
				if export_all:
					# Get list of tables to export
					output = query('SHOW TABLES')
					reader = csv.reader(output.split('\n'))
					export = [row[0] for row in reader][1:]

				print(query_export(export))
			except QueryException as e:
				sys.exit('ERROR %s' % e)
			sys.exit()

		# Handle stdin
		if not sys.stdin.isatty():
			q = sys.stdin.read()
			try:
				query_import(q)
			except QueryException as e:
				sys.exit('ERROR %s' % e)
			sys.exit()

		# Execute command
		if execute:
			try:
				print(query(execute))
			except QueryException as e:
				sys.exit('ERROR %s' % e)
			sys.exit()

		# Start interactive shell
		print("""Welcome to the phpMyAdmin command-line interface.

Copyright (c) 2014, fdev.nl. All rights reserved.

This application is not affiliated with or endorsed
by the phpMyAdmin Project or its trademark owners.
""")
		try:
			while True:
				if python2:
					q = raw_input('phpmyadmin> ')
				else:
					q = input('phpmyadmin> ')

				t0 = time.time()

				try:
					output = query(q)
					if output:
						reader = csv.reader(output.split('\n'))
						table = PrettyTable()
						if python2:
							table.field_names = [x.strip() for x in reader.next()]
						else:
							table.field_names = [x.strip() for x in next(reader)]
						table.align = 'l'
						for row in reader:
							data = [x.strip() for x in row]
							data.extend([''] * (len(table.field_names) - len(data)))
							table.add_row(data)
						print(table)
					print('Query OK, %d rows (%.2f sec)\n' % (table.rowcount if output else 0, time.time() - t0))

				except QueryException as e:
					print('ERROR %s' % e)

		except (EOFError, KeyboardInterrupt):
			print('Bye')

	except Exception as e:
		sys.exit('FATAL ERROR: %s' % e)

if __name__ == '__main__':
	main()
