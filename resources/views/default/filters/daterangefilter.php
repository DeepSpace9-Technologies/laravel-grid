<?php
$cfg = $filter->getConfig();
$inputName = $filter->getInputName();
$inputValue = $filter->getValue();
$config = json_encode(empty($cfg->get("config")) ? [] : $cfg->get("config"));
?>

<div class="datetimerangepicker">
    <input class="form-control" name="<?= htmlspecialchars($inputName); ?>"
           value="<?= htmlspecialchars($inputValue); ?>"/>
</div>

<script type="text/javascript">
    $('.datetimerangepicker>input[name="<?= htmlspecialchars($inputName); ?>"]').dateRangePicker(<?= $config; ?>)
        .bind('datepicker-change', function (event, obj) {
            $(this).closest('form').submit();
        });
</script>

