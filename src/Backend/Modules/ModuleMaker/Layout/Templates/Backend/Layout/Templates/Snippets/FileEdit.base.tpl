                            <div class="box">
                                <div class="heading">
                                    <h3>
                                        <label for="{$lower_ccased_label}">{$lbl{$camel_cased_label}|ucfirst}{$required_html}</label>
                                    </h3>
                                </div>
                                <div class="options">
                                    {option:item.{$underscored_label}}
                                        <p><a href="{$FRONTEND_FILES_URL}/{$module}/files/{$item.{$underscored_label}}">{$lblWatchFile}</a></p>
                                    {/option:item.{$underscored_label}}
                                    {$file{$camel_cased_label}} {$file{$camel_cased_label}Error}
                                </div>
                            </div>

