{include:{$BACKEND_CORE_PATH}/Layout/Templates/Head.tpl}
{include:{$BACKEND_CORE_PATH}/Layout/Templates/StructureStartModule.tpl}

<div class="pageTitle">
    <h2>
        {$lbl{$camel_case_name}|ucfirst}
    </h2>
    <div class="buttonHolderRight">
        <a href="{$var|geturl:'add'}" class="button icon iconAdd" title="{$lblAdd{$camel_case_name}|ucfirst}">
            <span>{$lblAdd{$camel_case_name}|ucfirst}</span>
        </a>
    </div>
</div>

{option:dataGrid}
    <div class="dataGridHolder">
        {$dataGrid}
    </div>
{/option:dataGrid}

{option:!dataGrid}
    {$msgNoItems}
{/option:!dataGrid}

{include:{$BACKEND_CORE_PATH}/Layout/Templates/StructureEndModule.tpl}
{include:{$BACKEND_CORE_PATH}/Layout/Templates/Footer.tpl}
