{include:file='{$BACKEND_CORE_PATH}/layout/templates/head.tpl'}
{include:file='{$BACKEND_CORE_PATH}/layout/templates/structure_start_module.tpl'}

<div class="pageTitle">
	<h2>{$lblUsers|ucfirst}: {$lblAdd}</h2>
</div>

{form:add}
	<div id="tabs" class="tabs">
		<ul>
			<li><a href="#tabProfile">{$lblProfile|ucfirst}</a></li>
			<li><a href="#tabPermissions">{$lblPermissions|ucfirst}</a></li>
		</ul>

		<div id="tabProfile">
			<div class="subtleBox">
				<div class="heading">
					<h3>{$lblLoginDetails|ucfirst}</h3>
				</div>
				<div class="options labelWidthLong horizontal">
					<p>
						<label for="email">{$lblEmail|ucfirst}<abbr title="{$lblRequiredField}">*</abbr></label>
						{$txtEmail} {$txtEmailError}
					</p>
					<p>
						<label for="password">{$lblPassword|ucfirst}<abbr title="{$lblRequiredField}">*</abbr></label>
						{$txtPassword} {$txtPasswordError}
					</p>
					<table id="passwordStrengthMeter" class="passwordStrength" rel="password" cellspacing="0">
						<tr>
							<td class="strength" id="passwordStrength">
								<p class="strength none">{$lblNone|ucfirst}</p>
								<p class="strength weak" style="background: red;">{$lblWeak|ucfirst}</p>
								<p class="strength ok" style="background: orange;">{$lblOK|ucfirst}</p>
								<p class="strength strong" style="background: green;">{$lblStrong|ucfirst}</p>
							</td>
							<td>
								<p class="helpTxt">{$msgHelpStrongPassword}</p>
							</td>
						</tr>
					</table>
					<p>
						<label for="confirmPassword">{$lblConfirmPassword|ucfirst}<abbr title="{$lblRequiredField}">*</abbr></label>
						{$txtConfirmPassword} {$txtConfirmPasswordError}
					</p>
				</div>
			</div>

			<div class="subtleBox">
				<div class="heading">
					<h3>{$lblPersonalInformation|ucfirst}</h3>
				</div>
				<div class="options labelWidthLong horizontal">
					<p>
						<label for="name">{$lblName|ucfirst}<abbr title="{$lblRequiredField}">*</abbr></label>
						{$txtName} {$txtNameError}
					</p>
					<p>
						<label for="surname">{$lblSurname|ucfirst}<abbr title="{$lblRequiredField}">*</abbr></label>
						{$txtSurname} {$txtSurnameError}
					</p>
					<p>
						<label for="nickname">{$lblNickname|ucfirst}<abbr title="{$lblRequiredField}">*</abbr></label>
						{$txtNickname} {$txtNicknameError}
						<span class="helpTxt">{$msgHelpNickname}</span>
					</p>
					<p>
						<label for="avatar">{$lblAvatar|ucfirst}</label>
						{$fileAvatar} {$fileAvatarError}
						<span class="helpTxt">{$msgHelpAvatar}</span>
					</p>
				</div>
			</div>

			<div class="subtleBox">
				<div class="heading">
					<h3>{$lblInterfacePreferences|ucfirst}</h3>
				</div>
				<div class="options labelWidthLong horizontal">
					<p>
						<label for="interfaceLanguage">{$lblLanguage|ucfirst}</label>
						{$ddmInterfaceLanguage} {$ddmInterfaceLanguageError}
					</p>
					<p>
						<label for="dateFormat">{$lblDateFormat|ucfirst}</label>
						{$ddmDateFormat} {$ddmDateFormatError}
					</p>
					<p>
						<label for="timeFormat">{$lblTimeFormat|ucfirst}</label>
						{$ddmTimeFormat} {$ddmTimeFormatError}
					</p>
				</div>
			</div>
		</div>

		<div id="tabPermissions">
			<div class="subtleBox">
				<div class="heading">
					<h3>{$lblAccountManagement|ucfirst}</h3>
				</div>
				<div class="options last horizontal">
					<ul class="inputList">
						<li>{$chkActive} <label for="active">{$msgHelpActive}</label> {$chkActiveError}</li>
						<li>{$chkApiAccess} <label for="active">{$msgHelpAPIAccess}</label> {$chkApiAccess}</li>
					</ul>
					<p>
						<label for="group">{$lblGroup|ucfirst}</label>
						{$ddmGroup} {$ddmGroupError}
					</p>
				</div>
			</div>
		</div>
	</div>

	<div class="fullwidthOptions">
		<div class="buttonHolderRight">
			<input id="addButton" class="button mainButton" type="submit" name="add" value="{$lblAdd|ucfirst}" />
		</div>
	</div>
{/form:add}

{include:file='{$BACKEND_CORE_PATH}/layout/templates/structure_end_module.tpl'}
{include:file='{$BACKEND_CORE_PATH}/layout/templates/footer.tpl'}