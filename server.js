const http = require('http');
const fs = require('fs');
const path = require('path');
const url = require('url');
const queryString = require('querystring');

const server = http.createServer((req, res) => {
  const { pathname, query } = url.parse(req.url, true);
  
  if (pathname === '/submit-request' && req.method === 'POST') {
    let body = '';
    req.on('data', chunk => {
      body += chunk.toString();
    });

    req.on('end', () => {
      const { name, songTitle, artist, dedication } = queryString.parse(body);

      const requestData = {
        name,
        songTitle,
        artist,
        dedication,
        timestamp: new Date().toISOString(),
      };

      try {
        const dataFilePath = path.join(__dirname, 'data.json');
        const existingData = fs.existsSync(dataFilePath)
          ? JSON.parse(fs.readFileSync(dataFilePath, 'utf8'))
          : [];

        existingData.push(requestData);

        fs.writeFileSync(dataFilePath, JSON.stringify(existingData, null, 2));

        res.writeHead(200, { 'Content-Type': 'text/plain' });
        res.end('Request submitted successfully');
      } catch (error) {
        console.error(error);
        res.writeHead(500, { 'Content-Type': 'text/plain' });
        res.end('Error submitting request');
      }
    });
  } else {
    // Handle other requests or serve HTML/JS files
    const indexPath = path.join(__dirname, 'index.html');
    const indexContent = fs.readFileSync(indexPath, 'utf8');
    
    res.writeHead(200, { 'Content-Type': 'text/html' });
    res.end(indexContent);
  }
});

const PORT = process.env.PORT || 3000;
server.listen(PORT, () => {
  console.log(`Server is running on port ${PORT}`);
});
