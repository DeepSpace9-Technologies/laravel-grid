<?php
$cfg = $filter->getConfig();
$inputName = $filter->getInputName();
$inputValue = $filter->getValue();
$config = json_encode(empty($cfg->get("config")) ? [] : $cfg->get("config"));
$uniqueId = uniqid('daterangepicker_');
?>
<link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css"/>
<script type="text/javascript" src="https://cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script>
<script type="text/javascript" src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>

<div class="datetimerangepicker" id="<?= $uniqueId ?>">
    <input class="form-control selected-date" name="<?= htmlspecialchars($inputName); ?>"
           value="<?= htmlspecialchars($inputValue); ?>"/>
</div>

<script type="text/javascript">
    $(document).ready(function() {
        var start = moment();
        var end = moment();
        var inputDateRange = "<?= htmlspecialchars($inputValue); ?>";
        if (inputDateRange) {
            var dates = inputDateRange.split(' to ');
            if (dates.length === 2) {
                start = moment(dates[0], 'DD-MM-YYYY');
                end = moment(dates[1], 'DD-MM-YYYY');
            }
        }

        function cb(start, end) {
            $('#<?= $uniqueId ?> span').html(start.format('MMMM D, YYYY') + ' - ' + end.format('MMMM D, YYYY'));
        }

        $('#<?= $uniqueId ?>').daterangepicker({
            startDate: start,
            endDate: end,
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

        cb(start, end);

        $('#<?= $uniqueId ?>').on('apply.daterangepicker', function (ev, picker) {
            $('#<?= $uniqueId ?> .selected-date').val(picker.startDate.format('DD-MM-YYYY') + ' to ' + picker.endDate.format('DD-MM-YYYY'));
            $('#<?= $uniqueId ?>').closest('form').submit();
        });
    });
</script>
