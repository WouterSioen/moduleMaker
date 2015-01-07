{include:{$BACKEND_CORE_PATH}/Layout/Templates/Head.tpl}
{include:{$BACKEND_CORE_PATH}/Layout/Templates/StructureStartModule.tpl}

<div class="pageTitle">
    <h2>{$lblModuleMaker|ucfirst}: {$lblWizardFields|ucfirst}</h2>

    <div class="buttonHolderRight">
        <a href="{$var|geturl:'add_field'}" class="button icon iconAdd" title="{$lblAddField|ucfirst}">
            <span>{$lblAddField|ucfirst}</span>
        </a>
    </div>
</div>

<div class="wizard">
    <ul>
        <li class="beforeSelected"><a href="{$var|geturl:'add'}"><b><span>1.</span> {$lblWizardInformation|ucfirst}</b></a></li>
        <li class="selected"><a href="{$var|geturl:'add_step2'}"><b><span>2.</span> {$lblWizardFields|ucfirst}</b></a></li>
        <li><b><span>3.</span> {$lblWizardSpecialFields|ucfirst}</b></li>
        <li><b><span>4.</span> {$lblWizardBlocks|ucfirst}</b></li>
        <li><b><span>5.</span> {$lblWizardGenerate|ucfirst}</b></li>
    </ul>
</div>
{option:datagrid}
    <div id="dataGridFieldsHolder">
        <h3>{$lblFields|ucfirst}</h3>
        <div class="dataGridHolder">
            {$datagrid}
        </div>
    </div>
{/option:datagrid}
{option:!datagrid}
    <p>{$msgNoFields}</p>
{/option:!datagrid}
{option:!varcharFound}
    <div id="pressMessage" class="generalMessage infoMessage content">
        <p class="pb0">{$msgWeNeedOneTextTypeForTheMeta|ucfirst}</p>
    </div>
{/option:!varcharFound}
<div class="fullwidthOptions">
    <div class="buttonHolder">
        <a id="toStep1" class="inputButton button" href="{$var|geturl:'add'}" >{$lblBack|ucfirst}</a>
    </div>
    <div class="buttonHolderRight">
        <a id="toStep3" class="inputButton button mainButton {option:!datagrid}disabledButton{/option:!datagrid} {option:!varcharFound}disabledButton{/option:!varcharFound}" href="{$var|geturl:'add_step3'}" >{$lblToStep|ucfirst} 3</a>
    </div>
</div>

{include:{$BACKEND_CORE_PATH}/Layout/Templates/StructureEndModule.tpl}
{include:{$BACKEND_CORE_PATH}/Layout/Templates/Footer.tpl}
