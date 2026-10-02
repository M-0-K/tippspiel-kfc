<?php
$publicUrl = getenv('PUBLIC_URL') ?: 'https://kulow-fighters.win';
$publicHost = preg_replace('#^https?://#', '', rtrim($publicUrl, '/'));
?>
    </main>

	<footer id="site-footer" class="fuss">
		<?= kfcDivider() ?>
		<p class="slogan">Respekt. Ehre. Kampf.</p>
		<div class="fuss-teams">
			<img src="../../data/logo/rote_funken.png" alt="Rote Funken" loading="lazy">
			<span class="vs-klein">VS</span>
			<img src="../../data/logo/lange_garde.png" alt="Lange Garde" loading="lazy">
		</div>
		<div id="presentation-footer-hint">
			Tippen &amp; Live-Daten unter
			<a href="<?= htmlspecialchars($publicUrl) ?>" target="_blank" rel="noopener noreferrer"><?= htmlspecialchars($publicHost) ?></a>
		</div>
	</footer>

</body>

</html>
