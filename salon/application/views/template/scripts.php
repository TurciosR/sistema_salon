<?php
defined('BASEPATH') or exit('No direct script access allowed');
?>

<!-- JS -->
<!-- Extra js -->
<?php if (isset($js)): ?>
    <?php foreach ($js as $extra => $url): ?>
        <script src="<?= base_url("assets/$url"); ?>"></script>
    <?php endforeach; ?>
<?php endif; ?>

</body>
</html>