<span>Showing records from</span>
<?php
/** @var Nayjest\Grids\Components\RecordsPerMonth $component */

if (is_array($component->getValue())) {
    $startDate = Carbon\Carbon::now()->subMonths((string)$component->getValue()[1])->format('d-m-Y');
    $endDate = Carbon\Carbon::now()->format('d-m-Y');
    $selectedDates = $startDate . ' to ' . $endDate;
} else {
    $dates = explode('to', $component->getValue());
    $startDate = Carbon\Carbon::parse($dates[0])->format('d-m-Y');
    $endDate = Carbon\Carbon::parse($dates[1])->format('d-m-Y');
    $selectedDates = $startDate . ' to ' . $endDate;
}
?>

<script type="text/javascript" src="https://cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script>
<script type="text/javascript" src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>
<link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css"/>

<span id="daterange" style="background: #fff; cursor: pointer; padding: 5px 10px; border: 1px solid #ccc; width: 100%">
    <i class="fa fa-calendar"></i>&nbsp;
    <span></span> <i class="fa fa-caret-down"></i>
    <input type="hidden" class="selected-date" name="<?= $component->getInputName() ?>" value="<?= htmlspecialchars($selectedDates) ?>">
</span>

<script>
    var start = moment("<?= $startDate ?>", "DD-MM-YYYY");
    var end = moment("<?= $endDate ?>", "DD-MM-YYYY");
    var dateRangeSelector = $('#daterange');

    function cb(start, end) {
        $('#daterange span').html(start.format("MMM D, YYYY") + ' - ' + end.format("MMM D, YYYY"));
        $('#datarange .selected-date').val(start.format('DD-MM-YYYY') + ' to ' + end.format('DD-MM-YYYY'));
    }

    dateRangeSelector.daterangepicker({
        autoApply: true,
        autoclose: true,
        ranges: {
            'Today': [moment(), moment()],
            'Yesterday': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
            'Last 30 Days': [moment().subtract(29, 'days'), moment()],
            'Last 3 Months': [moment().subtract(3, 'months').startOf('day'), moment()],
            'Last 6 Months': [moment().subtract(6, 'months').startOf('day'), moment()],
            'Last 9 Months': [moment().subtract(9, 'months').startOf('day'), moment()],
            'Last 1 Year': [moment().subtract(1, 'years').startOf('day'), moment()]
        }
    }, cb);

    dateRangeSelector.on('apply.daterangepicker', function (ev, picker) {
        $('#daterange .selected-date').val(picker.startDate.format('DD-MM-YYYY') + ' to ' + picker.endDate.format('DD-MM-YYYY'));
        $('#daterange').closest('form').submit();
    });

    cb(start, end);
</script>

