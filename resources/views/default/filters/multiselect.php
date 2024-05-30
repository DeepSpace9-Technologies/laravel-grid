<?php
/** @var Nayjest\Grids\Filter $filter */
/** @var Nayjest\Grids\MultiSelectFilterConfig $cfg */
$cfg = $filter->getConfig();
?>
<select
        class="form-control input-sm multi-select-option"
        name="<?= $filter->getInputName() . '[]' ?>" style="width: 100%" multiple
>
    <option value=""></option>
    <?php foreach ($filter->getConfig()->getOptions() as $value => $label): ?>
        <?php
        $selectedValue = ($filter->getValue() !== '' && $filter->getValue() !== null &&
            in_array($value, $filter->getValue())) ? 'selected="selected"' : '';
        ?>
        <option <?= $selectedValue ?> value="<?= $value ?>">
            <?= $label ?>
        </option>
    <?php endforeach ?>
</select>
<script>
    $(function () {
        let multiSelect = $('.multi-select-option');
        multiSelect.select2();

        multiSelect.on('select2:unselect select2:clear', function (e) {
            var $this = $(this);
            setTimeout(function () {
                if (!$this.val() || $this.val().length === 0) {
                    var form = $this.closest('form');
                    form.find('input').val('');
                    form.find('select').prop('selectedIndex', 0);
                    form.submit();
                    $this.select2('close');
                }
                $this.select2('close');
            }, 0);
        });
    });
</script>

