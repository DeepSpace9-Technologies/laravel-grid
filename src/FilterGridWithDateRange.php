<?php

namespace Nayjest\Grids;

use Carbon\Carbon;

class FilterGridWithDateRange
{
    /**
     * @var Grid
     */
    protected $grid;

    /**
     * Constructor.
     *
     * @param Grid $grid
     */
    public function __construct(Grid $grid)
    {
        $this->grid = $grid;
    }

    public function apply()
    {
        $timeZone = config('grid_setting.timezone');
        $appliedFilters = $this->grid->getConfig()->getDataProvider()->getGridDateRangeFilter();
        if (!empty($appliedFilters)) {
            $relationAndColumnName = explode('.', $appliedFilters[0]);
            if (is_numeric($appliedFilters[1])) {
                $startDate = Carbon::now($timeZone['current_timezone'])->subMonths($appliedFilters[1])->startOfDay()->setTimezone($timeZone['selected_timezone'])->toDateTimeString();
                $endDate = Carbon::now($timeZone['current_timezone'])->endOfDay()->setTimezone($timeZone['selected_timezone'])->toDateTimeString();
            } else {
                $dateRange = explode(" to ", $appliedFilters[1]);
                $startDate = Carbon::parse($dateRange[0], $timeZone['current_timezone'])->startOfDay()->setTimezone($timeZone['selected_timezone'])->toDateTimeString();
                $endDate = Carbon::parse($dateRange[1], $timeZone['current_timezone'])->endOfDay()->setTimezone($timeZone['selected_timezone'])->toDateTimeString();
            }
            $this->grid
                ->getConfig()
                ->getDataProvider()
                ->dateTimeRangeFilter($relationAndColumnName, [$startDate, $endDate]);
        }
    }
}
