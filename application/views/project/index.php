
<h2>
    <?php echo $title; ?>
</h2>

<?php foreach ($projects as $project_item): ?>

<h3>
    <?php echo $project_item['id']; ?>
</h3>
<div class="main">
    <?php echo $project_item['effort_target']; ?>
</div>
<p>
    <a href="<?php echo site_url('project/'.$project_item['id']); ?>">View Project</a>
</p>

<?php endforeach; ?>