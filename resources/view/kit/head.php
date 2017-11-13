	<head>
            <meta name="robots" content="noindex">
            <meta charset="UTF-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=1">
            <title><?= isset($title) ? self::safe($title) : '' ?></title>  
            
            <link rel="stylesheet" href="<?= self::assembleCSS('vendor/reset.css', 'core.less') ?>" />
            
            <script src="<?= self::assembleJS('vendor/jquery-3.2.1.min.js','vendor/handmade.js', 'core.js') ?>"></script>
            
	</head>