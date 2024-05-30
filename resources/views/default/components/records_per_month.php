<span>Showing records from</span>
<?php
/** @var Nayjest\Grids\Components\RecordsPerMonth $component */
if (is_array($component->getValue())) {
    $starDate = Carbon\Carbon::now()->subMonths((string)$component->getValue()[1])->format('Y-m-d');
    $endDate = Carbon\Carbon::now()->format('Y-m-d');
    $selectedDates = $starDate . ' to ' . $endDate;
} else {
    $dates = explode('to', $component->getValue());
    $starDate = Carbon\Carbon::parse($dates[0])->format('d-m-Y');
    $endDate = Carbon\Carbon::parse($dates[1])->format('d-m-Y');
    $selectedDates = $starDate . ' to ' . $endDate;
}
?>

<span class="datetimerangepicker">
    <input class="border-grey input-sm" name="<?= htmlspecialchars($component->getInputName()) ?>"
           value="<?= htmlspecialchars($selectedDates) ?>"/>
</span>

<script type="text/javascript">
    $('.datetimerangepicker>input[name="<?= htmlspecialchars($component->getInputName()) ?>"]').dateRangePicker({
        autoClose: true,
    }).bind('datepicker-change', function () {
        $(this).closest('form').submit();
    });
</script>
