							<div class="box">
								<div class="heading">
									<h3>
										{$lbl{$camel_cased_label}|ucfirst}
									</h3>
								</div>
								<div class="options">
									<ul class="inputList">
										{iteration:{$underscored_label}}
											<li>
												{${$underscored_label}.rbt{$camel_cased_label}}
												<label for="{${$underscored_label}.id}">{${$underscored_label}.label}</label>
											</li>
										{/iteration:{$underscored_label}}
									</ul>
								</div>
							</div>

