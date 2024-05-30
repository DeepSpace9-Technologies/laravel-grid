<?php

namespace Nayjest\Grids\Core;

use Nayjest\Grids\Components\CsvExport as Component;

class CsvExport extends Component
{
    const CSV_DELIMITER = ',';
    const DEFAULT_ROWS_LIMIT = 10000;

    protected $rows_limit = self::DEFAULT_ROWS_LIMIT;

}
