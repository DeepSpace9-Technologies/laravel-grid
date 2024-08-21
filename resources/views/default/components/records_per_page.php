<span>Records </span>
<?php
/** @var Nayjest\Grids\Components\RecordsPerPage $component */

echo Spatie\Html\Facades\Html::select(
    $component->getInputName(),
    $component->getVariants(),
    $component->getValue()
)
    ->class('form-control input-sm grids-control-records-per-page')
    ->attribute('style', 'display: inline; width: auto;');
?>
<span style="margin-right: 10px"> per page</span>
