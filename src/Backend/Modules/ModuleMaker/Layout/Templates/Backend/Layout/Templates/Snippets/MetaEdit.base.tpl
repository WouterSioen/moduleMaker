    <label for="{$lower_ccased_label}">{$lbl{$camel_cased_label}|ucfirst}</label>
    {$txt{$camel_cased_label}} {$txt{$camel_cased_label}Error}

    <div id="pageUrl">
        <div class="oneLiner">
            {option:detailURL}<p><span><a href="{$detailURL}/{$item.url}">{$detailURL}/<span id="generatedUrl">{$item.url}</span></a></span></p>{/option:detailURL}
            {option:!detailURL}<p class="infoMessage">{$errNoModuleLinked}</p>{/option:!detailURL}
        </div>
    </div>

