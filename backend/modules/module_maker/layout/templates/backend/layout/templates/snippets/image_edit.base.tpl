							<div class="box">
								<div class="heading">
									<h3>
										<label for="{$lower_ccased_label}">{$lbl{$camel_cased_label}|ucfirst}{$required_html}</label>
									</h3>
								</div>
								<div class="options">
									{option:item.{$underscored_label}}
										<img src="{$FRONTEND_FILES_URL}/{$module}/{$underscored_label}/{$image_size}/{$item.{$underscored_label}}"/>
									{/option:item.{$underscored_label}}
									{$file{$camel_cased_label}} {$file{$camel_cased_label}Error}
								</div>
							</div>

