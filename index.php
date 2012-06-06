<?php
/*
 * index.php
 */
require_once 'config.inc.php';


$rows = $dba->find_application_list();

?>
<!DOCTYPE html>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>iOS App Install</title>
<link rel="stylesheet"
	href="http://code.jquery.com/mobile/1.0/jquery.mobile-1.0.min.css" />
<style type="text/css">
dl.ipa {
	cursor: pointer;
	background-coloer: #FFF;
	border: 1px solid #ccc;
}
	
</style>

<script src="http://code.jquery.com/jquery-1.6.4.min.js"></script>
<script src="http://code.jquery.com/mobile/1.0/jquery.mobile-1.0.min.js"></script>

</head>
<body>
	<div data-role="page">
		<div data-role="header">
			<h1>アプリケーションリスト</h1>
		</div>
		<div data-role="content">
		<?php
		if (is_null($rows)) {
		?>
			<div>アプリが登録されていません</div>
		<?php 
		} else {
			?><ul data-role="listview" data-theme="g"><?php
			foreach($rows as $row) {
			?>
			<li class="ipa">
				<a href="detail.php?ipa=<?php echo $row['key']; ?>">
					<h3 class="ui-li-heading"><?php echo h($row['file_name']);?>(<?php echo h($row['name']); ?>)</h3>
					<p class="ui-li-desc">
						<span><b>リリースバージョン</b>: <?php echo $row['version'];?></span>
						<br />
						<span><b>アップロード日時</b>: <?php echo $row['timestamp'];?></span>
						<br />
						<?php if (strlen($row['memo'])) { ?>
						<span><b>メモ</b>：<?php echo h($row['memo']); ?></span>
						<?php } ?>
					</p>
					<p class="ui-li-desc"></p>
				</a>
			</li>
			<?php 
			}
			?>
			</ul>
			<?php
		}
		?>
		<a href="upload.php" data-role="button">新規登録</a>
		</div>
		<div data-role="footer">
			<h4>(C) SimplePHPAdhocServer</h4>
		</div>
	</div>
</body>
</html>
