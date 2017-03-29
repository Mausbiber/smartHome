<div id="sidebar-wrapper">
    <div class="list-group">
        <a href="../dashboard/dashboard.php" class="list-group-item"><nobr><?php echo htmlspecialchars($lang['dashboard']); ?></nobr></a>
        <a href="../scheduler/" class="list-group-item"><nobr><?php echo htmlspecialchars($lang['time_switch']); ?></nobr></a>
      
	  <a href="#settings" class="list-group-item" data-toggle="collapse" data-parent="#sidebar-wrapper"><nobr><?php echo htmlspecialchars($lang['settings']); ?></nobr></a>
        <div class="collapse" id="settings">
            <a href="../settings/index.php" class="list-group-item list-group-item-sub"><nobr><?php echo htmlspecialchars($lang['general']); ?></nobr></a>
            <a href="../settings/switches.php" class="list-group-item list-group-item-sub"><nobr><?php echo htmlspecialchars($lang['switches']); ?></nobr></a>
            <a href="../settings/sensors.php"   class="list-group-item list-group-item-sub"><nobr><?php echo htmlspecialchars($lang['sensors']); ?></nobr></a>
        </div>
    </div>
</div>
