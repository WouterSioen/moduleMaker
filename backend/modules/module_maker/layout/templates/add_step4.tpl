{include:{$BACKEND_CORE_PATH}/layout/templates/head.tpl}
{include:{$BACKEND_CORE_PATH}/layout/templates/structure_start_module.tpl}

<div class="pageTitle">
	<h2>{$lblModuleMaker|ucfirst}: {$lblWizardBlocks|ucfirst}</h2>
</div>

<div class="wizard">
	<ul>
		<li><a href="{$var|geturl:'add'}"><b><span>1.</span> {$lblWizardInformation|ucfirst}</b></a></li>
		<li><a href="{$var|geturl:'add_step2'}"><b><span>2.</span> {$lblWizardFields|ucfirst}</b></a></li>
		<li class="beforeSelected"><a href="{$var|geturl:'add_step3'}"><b><span>3.</span> {$lblWizardSpecialFields|ucfirst}</b></a></li>
		<li class="selected"><a href="{$var|geturl:'add_step4'}"><b><span>4.</span> {$lblWizardBlocks|ucfirst}</b></a></li>
		<li><b><span>5.</span> {$lblWizardGenerate|ucfirst}</b></li>
	</ul>
</div>

{form:add_step4}
	<div class="box">
		<div class="heading">
			<h3>{$lblWizardBlocks|ucfirst}</h3>
		</div>
		<div class="options">
			<p>
				<label for="twitter">{$chkTwitter} {$lblTwitterCard|ucfirst}</label>
			</p>
			<span class="showOnTwitter"{option:!item.twitter} style="display: none;"{/option:!item.twitter}>
				<label for="twitterName">{$lblTwitterName|ucfirst}</label>
				@{$txtTwitterName} {$txtTwitterNameError}
			</span>
		</div>
	</div>

	<div class="fullwidthOptions">
		<div class="buttonHolderRight">
			<input id="addStep4" class="inputButton button mainButton" type="submit" name="add_step_4" value="{$lblGenerate|ucfirst}" />
		</div>
	</div>
{/form:add_step4}

{include:{$BACKEND_CORE_PATH}/layout/templates/structure_end_module.tpl}
{include:{$BACKEND_CORE_PATH}/layout/templates/footer.tpl}