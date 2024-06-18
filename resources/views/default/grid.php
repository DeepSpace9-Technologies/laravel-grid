<form>
    <?php
    /** @var Nayjest\Grids\DataProvider $data **/
    /** @var Nayjest\Grids\Grid $grid **/
    ?>
    <style>
        .loader {
            position: fixed;
            left: 55%;
            top: 55%;
            transform: translate(-50%, -50%);
            z-index: 9999;
            width: 40px;
            height: 40px;
            background-color: #3498db;
            border-radius: 100%;
            animation: sk-scaleout 1s infinite ease-in-out;
        }

        @keyframes sk-scaleout {
            0% { transform: scale(0) }
            100% {
                transform: scale(1.0);
                opacity: 0;
            }
        }
    </style>
    <div id="loader" class="loader" style="display: none"></div>
    <table class="table table-striped" id="<?= $grid->getConfig()->getName() ?>">
        <?= $grid->header() ? $grid->header()->render() : '' ?>
        <?php # ========== TABLE BODY ========== ?>
        <tbody>
        <?php while($row = $data->getRow()): ?>
            <?= $grid->getConfig()->getRowComponent()->setDataRow($row)->render() ?>
        <?php endwhile; ?>
        </tbody>
        <?= $grid->footer() ? $grid->footer()->render() : '' ?>
        <?php # Hidden input for submitting form by pressing enter if there are no other submits ?>
        <input type="submit" style="position: absolute; left: -9999px; width: 1px; height: 1px;" tabindex="-1" />
    </table>
</form>
