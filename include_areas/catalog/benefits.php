<div class="row benefits hidden-xs wow fadeIn">
	<div class="benefit">
		<div class="img-wrap">
			<span data-picture data-alt="Качественный сервис!">
			<span data-src="<?=SITE_TEMPLATE_PATH?>/pictures/benefits/OK.png"></span>
			<span data-src="" data-media="(max-width: 767px)"></span>

			<!-- Fallback content for non-JS browsers. Same img src as the initial, unqualified source element. -->
				<noscript>
				 	<img src="<?=SITE_TEMPLATE_PATH?>/pictures/benefits/OK.png" alt="Качественный сервис!">
				</noscript>
			</span>
		</div>
		<div class="content">
			<header align="center">Качественный сервис</header>
            <p align="center">Профессиональные консультации по любым вопросам.</p>
		</div>
	</div>

	<? /*<div class="benefit" style="display:none">
	<div class="img-wrap">
			<span data-picture data-alt="Гарантируем!">
				<span data-src="<?=SITE_TEMPLATE_PATH?>/pictures/benefits/OK.png"></span>
				<span data-src="" data-media="(max-width: 767px)"></span>

				<!-- Fallback content for non-JS browsers. Same img src as the initial, unqualified source element. -->
				<noscript>
					<img src="<?=SITE_TEMPLATE_PATH?>/pictures/benefits/OK.png" alt="Гарантируем!">
				</noscript>
			</span>
		</div>
		<div class="content">
			<header>Качественный сервис</header>
                        <p>Профессиональные консультации по любым вопросам.    </p>
		</div>
	</div>

	*/ ?>
		<?
	global $garantiya;
	if(!empty($garantiya["VALUE"])):

		switch($garantiya["VALUE"]){
			
			case 1:
				$garantiya["VALUE"] = "1 год";
				break;

			case 14:
				$garantiya["VALUE"] = "2 недели";
				break;
		}
		
		?>
	<div class="benefit">
		<div class="img-wrap">
			<span data-picture data-alt="Обмениваем!">
			<span data-src="<?=SITE_TEMPLATE_PATH?>/pictures/benefits/exchange.png"></span>
			<span data-src="" data-media="(max-width: 767px)"></span>

			<!-- Fallback content for non-JS browsers. Same img src as the initial, unqualified source element. -->
			<noscript>
				<img src="<?=SITE_TEMPLATE_PATH?>/pictures/benefits/exchange.png" alt="Обмениваем!">
			</noscript>
			</span>		
		</div>
		<div class="content">
			<header align="center">Гарантия <?=$garantiya["VALUE"]?></header>
                        <p align="center">На данный товар выдается гарантийный талон сроком на <?=$garantiya["VALUE"]?> </p>
		</div>
	</div>
	<?endif;?>
</div>
