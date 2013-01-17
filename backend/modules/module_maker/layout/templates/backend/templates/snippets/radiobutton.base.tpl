							<div class="box">
								<div class="heading">
									<h3>
										<label for="{$lower_ccased_label}">{$lbl{$camel_cased_label}|ucfirst}</label>
									</h3>
								</div>
								<div class="options">
									<ul class="inputList">
										{iteration:{$underscored_label}}
											<li>{$underscored_label.rbt{$camel_cased_label}}</li>
										{/iteration:{$underscored_label}}
									</ul>
								</div>
							</div>