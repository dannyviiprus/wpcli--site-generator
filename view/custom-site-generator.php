<?php

use Dvpz\CustomSiteGenerator;

$customSiteGenerator = new CustomSiteGenerator();

$generateForm = $customSiteGenerator->getForm();
$generateForm->action = '';

if ($generateForm->submitted()) {
    $output = $customSiteGenerator->generateSite($generateForm);
}

?>

<div class="container">
    <div class="container" style="max-width: 1200px; padding: 80px">
        <div class="form-wrapper">
            <?php $generateForm->open('generateForm', 'generateForm', '', 'POST'); ?>
                <?php $generateForm->hidden('formID', 'generateForm'); ?>
                <div class="form-group mb-3">
                    <?php $generateForm->number('numberOfSite', 'Number of site to generate:', '1', 'numberOfSite', 'class="form-control"'); ?>
                </div>
                <div class="form-group text-center" bis_skin_checked="1">
                    <?php $generateForm->input_button_submit('button','', __('Generate', 'awsb'), 'submit', 'class="btn btn-submit m-b-15 w-100"'); ?>
                    <?php $generateForm->input_button_submit('clean','', __('Clean Sites', 'awsb'), 'clean', 'class="btn btn-submit m-b-15 w-100"'); ?>
                </div>
            <?php $generateForm->close(); ?>
            <div class="console" style="height: 350px; overflow-y: scroll; padding: 30px; background: #e1e1e1; margin-top: 30px;">
                <?php
                    if (!empty($output)) {
                        foreach ($output as $item) {
                            dump($item);
                        }
                    }
                ?>
            </div>
            <table class="table table-dark table-sm" style="margin-top: 30px;">
                <thead>
                    <tr>
                        <th scope="col">#</th>
                        <th scope="col">Site Name</th>
                        <th scope="col">Site Path</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach($customSiteGenerator->getGeneratedSites() as $index => $site): ?>
                    <tr>
                        <th scope="row"><?php echo $index; ?></th>
                        <td><?php echo $site; ?></td>
                        <td><?php echo ABSPATH . $site; ?></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
