<?php

declare(strict_types=1);

namespace PhpMyAdmin\Config\Settings;

// phpcs:disable Squiz.NamingConventions.ValidVariableName.MemberNotCamelCaps

use function in_array;

/**
 * Schema export defaults
 *
 * @psalm-immutable
 * @psalm-type SchemaSettingsType = array{
 *     format: string,
 *     pdf_show_color: bool,
 *     pdf_show_keys: bool,
 *     pdf_all_tables_same_width: bool,
 *     pdf_orientation: 'L'|'P',
 *     pdf_paper: string,
 *     pdf_show_grid: bool,
 *     pdf_with_doc: bool,
 *     pdf_table_order: ''|'name_asc'|'name_desc',
 *     dia_show_color: bool, dia_show_keys: bool,
 *     dia_orientation: 'L'|'P',
 *     dia_paper: string,
 *     eps_show_color: bool,
 *     eps_show_keys: bool,
 *     eps_all_tables_same_width: bool,
 *     eps_orientation: 'L'|'P',
 *     svg_show_color: bool,
 *     svg_show_keys: bool,
 *     svg_all_tables_same_width: bool,
 * }
 */
final class Schema
{
    /**
     * ```php
     * $cfg['Schema']['format'] = 'pdf';
     * ```
     *
     * @psalm-var 'pdf'|'eps'|'dia'|'svg'
     */
    public string $format;

    /**
     * ```php
     * $cfg['Schema']['pdf_show_color'] = true;
     * ```
     */
    public bool $pdf_show_color;

    /**
     * ```php
     * $cfg['Schema']['pdf_show_keys'] = false;
     * ```
     */
    public bool $pdf_show_keys;

    /**
     * ```php
     * $cfg['Schema']['pdf_all_tables_same_width'] = false;
     * ```
     */
    public bool $pdf_all_tables_same_width;

    /**
     * ```php
     * $cfg['Schema']['pdf_orientation'] = 'L';
     * ```
     *
     * @psalm-var 'L'|'P'
     */
    public string $pdf_orientation;

    /**
     * ```php
     * $cfg['Schema']['pdf_paper'] = 'A4';
     * ```
     */
    public string $pdf_paper;

    /**
     * ```php
     * $cfg['Schema']['pdf_show_grid'] = false;
     * ```
     */
    public bool $pdf_show_grid;

    /**
     * ```php
     * $cfg['Schema']['pdf_with_doc'] = true;
     * ```
     */
    public bool $pdf_with_doc;

    /**
     * ```php
     * $cfg['Schema']['pdf_table_order'] = '';
     * ```
     *
     * @psalm-var ''|'name_asc'|'name_desc'
     */
    public string $pdf_table_order;

    /**
     * ```php
     * $cfg['Schema']['dia_show_color'] = true;
     * ```
     */
    public bool $dia_show_color;

    /**
     * ```php
     * $cfg['Schema']['dia_show_keys'] = false;
     * ```
     */
    public bool $dia_show_keys;

    /**
     * ```php
     * $cfg['Schema']['dia_orientation'] = 'L';
     * ```
     *
     * @psalm-var 'L'|'P'
     */
    public string $dia_orientation;

    /**
     * ```php
     * $cfg['Schema']['dia_paper'] = 'A4';
     * ```
     */
    public string $dia_paper;

    /**
     * ```php
     * $cfg['Schema']['eps_show_color'] = true;
     * ```
     */
    public bool $eps_show_color;

    /**
     * ```php
     * $cfg['Schema']['eps_show_keys'] = false;
     * ```
     */
    public bool $eps_show_keys;

    /**
     * ```php
     * $cfg['Schema']['eps_all_tables_same_width'] = false;
     * ```
     */
    public bool $eps_all_tables_same_width;

    /**
     * ```php
     * $cfg['Schema']['eps_orientation'] = 'L';
     * ```
     *
     * @psalm-var 'L'|'P'
     */
    public string $eps_orientation;

    /**
     * ```php
     * $cfg['Schema']['svg_show_color'] = true;
     * ```
     */
    public bool $svg_show_color;

    /**
     * ```php
     * $cfg['Schema']['svg_show_keys'] = false;
     * ```
     */
    public bool $svg_show_keys;

    /**
     * ```php
     * $cfg['Schema']['svg_all_tables_same_width'] = false;
     * ```
     */
    public bool $svg_all_tables_same_width;

    /** @param array<int|string, mixed> $schema */
    public function __construct(array $schema = [])
    {
        $this->format = $this->setFormat($schema);
        $this->pdf_show_color = $this->setPdfShowColor($schema);
        $this->pdf_show_keys = $this->setPdfShowKeys($schema);
        $this->pdf_all_tables_same_width = $this->setPdfAllTablesSameWidth($schema);
        $this->pdf_orientation = $this->setPdfOrientation($schema);
        $this->pdf_paper = $this->setPdfPaper($schema);
        $this->pdf_show_grid = $this->setPdfShowGrid($schema);
        $this->pdf_with_doc = $this->setPdfWithDoc($schema);
        $this->pdf_table_order = $this->setPdfTableOrder($schema);
        $this->dia_show_color = $this->setDiaShowColor($schema);
        $this->dia_show_keys = $this->setDiaShowKeys($schema);
        $this->dia_orientation = $this->setDiaOrientation($schema);
        $this->dia_paper = $this->setDiaPaper($schema);
        $this->eps_show_color = $this->setEpsShowColor($schema);
        $this->eps_show_keys = $this->setEpsShowKeys($schema);
        $this->eps_all_tables_same_width = $this->setEpsAllTablesSameWidth($schema);
        $this->eps_orientation = $this->setEpsOrientation($schema);
        $this->svg_show_color = $this->setSvgShowColor($schema);
        $this->svg_show_keys = $this->setSvgShowKeys($schema);
        $this->svg_all_tables_same_width = $this->setSvgAllTablesSameWidth($schema);
    }

    /** @psalm-return SchemaSettingsType */
    public function asArray(): array
    {
        return [
            'format' => $this->format,
            'pdf_show_color' => $this->pdf_show_color,
            'pdf_show_keys' => $this->pdf_show_keys,
            'pdf_all_tables_same_width' => $this->pdf_all_tables_same_width,
            'pdf_orientation' => $this->pdf_orientation,
            'pdf_paper' => $this->pdf_paper,
            'pdf_show_grid' => $this->pdf_show_grid,
            'pdf_with_doc' => $this->pdf_with_doc,
            'pdf_table_order' => $this->pdf_table_order,
            'dia_show_color' => $this->dia_show_color,
            'dia_show_keys' => $this->dia_show_keys,
            'dia_orientation' => $this->dia_orientation,
            'dia_paper' => $this->dia_paper,
            'eps_show_color' => $this->eps_show_color,
            'eps_show_keys' => $this->eps_show_keys,
            'eps_all_tables_same_width' => $this->eps_all_tables_same_width,
            'eps_orientation' => $this->eps_orientation,
            'svg_show_color' => $this->svg_show_color,
            'svg_show_keys' => $this->svg_show_keys,
            'svg_all_tables_same_width' => $this->svg_all_tables_same_width,
        ];
    }

    /**
     * @param array<int|string, mixed> $schema
     *
     * @psalm-return 'pdf'|'eps'|'dia'|'svg'
     */
    private function setFormat(array $schema): string
    {
        if (isset($schema['format']) && in_array($schema['format'], ['eps', 'dia', 'svg'], true)) {
            return $schema['format'];
        }

        return 'pdf';
    }

    /** @param array<int|string, mixed> $schema */
    private function setPdfShowColor(array $schema): bool
    {
        if (isset($schema['pdf_show_color'])) {
            return (bool) $schema['pdf_show_color'];
        }

        return true;
    }

    /** @param array<int|string, mixed> $schema */
    private function setPdfShowKeys(array $schema): bool
    {
        if (isset($schema['pdf_show_keys'])) {
            return (bool) $schema['pdf_show_keys'];
        }

        return false;
    }

    /** @param array<int|string, mixed> $schema */
    private function setPdfAllTablesSameWidth(array $schema): bool
    {
        if (isset($schema['pdf_all_tables_same_width'])) {
            return (bool) $schema['pdf_all_tables_same_width'];
        }

        return false;
    }

    /**
     * @param array<int|string, mixed> $schema
     *
     * @psalm-return 'L'|'P'
     */
    private function setPdfOrientation(array $schema): string
    {
        if (isset($schema['pdf_orientation']) && $schema['pdf_orientation'] === 'P') {
            return 'P';
        }

        return 'L';
    }

    /** @param array<int|string, mixed> $schema */
    private function setPdfPaper(array $schema): string
    {
        if (isset($schema['pdf_paper'])) {
            return (string) $schema['pdf_paper'];
        }

        return 'A4';
    }

    /** @param array<int|string, mixed> $schema */
    private function setPdfShowGrid(array $schema): bool
    {
        if (isset($schema['pdf_show_grid'])) {
            return (bool) $schema['pdf_show_grid'];
        }

        return false;
    }

    /** @param array<int|string, mixed> $schema */
    private function setPdfWithDoc(array $schema): bool
    {
        if (isset($schema['pdf_with_doc'])) {
            return (bool) $schema['pdf_with_doc'];
        }

        return true;
    }

    /**
     * @param array<int|string, mixed> $schema
     *
     * @psalm-return ''|'name_asc'|'name_desc'
     */
    private function setPdfTableOrder(array $schema): string
    {
        if (
            isset($schema['pdf_table_order']) && in_array($schema['pdf_table_order'], ['name_asc', 'name_desc'], true)
        ) {
            return $schema['pdf_table_order'];
        }

        return '';
    }

    /** @param array<int|string, mixed> $schema */
    private function setDiaShowColor(array $schema): bool
    {
        if (isset($schema['dia_show_color'])) {
            return (bool) $schema['dia_show_color'];
        }

        return true;
    }

    /** @param array<int|string, mixed> $schema */
    private function setDiaShowKeys(array $schema): bool
    {
        if (isset($schema['dia_show_keys'])) {
            return (bool) $schema['dia_show_keys'];
        }

        return false;
    }

    /**
     * @param array<int|string, mixed> $schema
     *
     * @psalm-return 'L'|'P'
     */
    private function setDiaOrientation(array $schema): string
    {
        if (isset($schema['dia_orientation']) && $schema['dia_orientation'] === 'P') {
            return 'P';
        }

        return 'L';
    }

    /** @param array<int|string, mixed> $schema */
    private function setDiaPaper(array $schema): string
    {
        if (isset($schema['dia_paper'])) {
            return (string) $schema['dia_paper'];
        }

        return 'A4';
    }

    /** @param array<int|string, mixed> $schema */
    private function setEpsShowColor(array $schema): bool
    {
        if (isset($schema['eps_show_color'])) {
            return (bool) $schema['eps_show_color'];
        }

        return true;
    }

    /** @param array<int|string, mixed> $schema */
    private function setEpsShowKeys(array $schema): bool
    {
        if (isset($schema['eps_show_keys'])) {
            return (bool) $schema['eps_show_keys'];
        }

        return false;
    }

    /** @param array<int|string, mixed> $schema */
    private function setEpsAllTablesSameWidth(array $schema): bool
    {
        if (isset($schema['eps_all_tables_same_width'])) {
            return (bool) $schema['eps_all_tables_same_width'];
        }

        return false;
    }

    /**
     * @param array<int|string, mixed> $schema
     *
     * @psalm-return 'L'|'P'
     */
    private function setEpsOrientation(array $schema): string
    {
        if (isset($schema['eps_orientation']) && $schema['eps_orientation'] === 'P') {
            return 'P';
        }

        return 'L';
    }

    /** @param array<int|string, mixed> $schema */
    private function setSvgShowColor(array $schema): bool
    {
        if (isset($schema['svg_show_color'])) {
            return (bool) $schema['svg_show_color'];
        }

        return true;
    }

    /** @param array<int|string, mixed> $schema */
    private function setSvgShowKeys(array $schema): bool
    {
        if (isset($schema['svg_show_keys'])) {
            return (bool) $schema['svg_show_keys'];
        }

        return false;
    }

    /** @param array<int|string, mixed> $schema */
    private function setSvgAllTablesSameWidth(array $schema): bool
    {
        if (isset($schema['svg_all_tables_same_width'])) {
            return (bool) $schema['svg_all_tables_same_width'];
        }

        return false;
    }
}
