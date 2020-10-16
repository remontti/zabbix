<?php
/*
** Zabbix
** Copyright (C) 2001-2020 Zabbix SIA
**
** This program is free software; you can redistribute it and/or modify
** it under the terms of the GNU General Public License as published by
** the Free Software Foundation; either version 2 of the License, or
** (at your option) any later version.
**
** This program is distributed in the hope that it will be useful,
** but WITHOUT ANY WARRANTY; without even the implied warranty of
** MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
** GNU General Public License for more details.
**
** You should have received a copy of the GNU General Public License
** along with this program; if not, write to the Free Software
** Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.
**/


/**
 * @var CView $this
 */

$this->includeJsFile('administration.userrole.edit.js.php');

$widget = (new CWidget())->setTitle(_('User roles'));

$form = (new CForm())
	->setId('userrole-form')
	->setName('user_role_form')
	->setAttribute('aria-labeledby', ZBX_STYLE_PAGE_TITLE);

if ($data['roleid'] != 0) {
	$form->addVar('roleid', $data['roleid']);
}

$form_grid = (new CFormGrid())->addClass(CFormGrid::ZBX_STYLE_FORM_GRID_1_1);

$form_grid->addItem([
	(new CLabel(_('Name'), 'name'))->setAsteriskMark(),
	(new CFormField(
		(new CTextBox('name', $data['name'], $data['readonly'], DB::getFieldLength('role', 'name')))
			->setWidth(ZBX_TEXTAREA_STANDARD_WIDTH)
			->setAriaRequired()
			->setAttribute('autofocus', 'autofocus')
			->setAttribute('maxlength', DB::getFieldLength('role', 'name'))
	))->addClass(CFormField::ZBX_STYLE_FORM_FIELD_FLUID)
]);

if ($data['readonly']) {
	$form_grid->addItem([
		(new CLabel(_('User type'), 'type')),
		(new CFormField(
			(new CTextBox('type', user_type2str()[$data['type']]))->setAttribute('readonly', true)
		))->addClass(CFormField::ZBX_STYLE_FORM_FIELD_FLUID)
	]);
}
else {
	$form_grid->addItem([
		(new CLabel(_('User type'), 'type')),
		(new CFormField(
			(new CComboBox('type', $data['type'], null, user_type2str()))->addClass('js-userrole-usertype')
		))->addClass(CFormField::ZBX_STYLE_FORM_FIELD_FLUID)
	]);
}

$form_grid->addItem(
	(new CFormField((new CTag('h4', true, _('Access to UI elements')))->addClass('input-section')))
		->addClass(CFormField::ZBX_STYLE_FORM_FIELD_FLUID)
		->addClass(CFormField::ZBX_STYLE_FORM_FIELD_OFFSET_1)
);

foreach ($data['labels']['sections'] as $section_key => $section_label) {
	$ui = [];
	foreach ($data['labels']['rules'][$section_key] as $rule_key => $rule_label) {
		$ui[] = new CDiv(
			(new CCheckBox($rule_key, 1))
				->setChecked(array_key_exists($rule_key, $data['rules']) && $data['rules'][$rule_key])
				->setReadonly($data['readonly'])
				->setLabel($rule_label)
		);
	}
	$form_grid->addItem([
		new CLabel($section_label, $section_key),
		(new CFormField(
			(new CDiv(
				(new CDiv($ui))
					->addClass(ZBX_STYLE_COLUMNS)
					->addClass(ZBX_STYLE_COLUMNS_3)
			))->setWidth(ZBX_TEXTAREA_STANDARD_WIDTH)
		))->addClass(CFormField::ZBX_STYLE_FORM_FIELD_FLUID)
	]);
}

if (!$data['readonly']) {
	$form_grid->addItem(
		(new CFormField((new CLabel(_('At least one UI element must be checked.')))->setAsteriskMark()))
			->addClass(CFormField::ZBX_STYLE_FORM_FIELD_FLUID)
			->addClass(CFormField::ZBX_STYLE_FORM_FIELD_OFFSET_1)
	);
}

$form_grid->addItem([
	new CLabel(_('Default access to new UI elements'), 'ui.default_access'),
	(new CFormField(
		(new CCheckBox('ui.default_access', 1))
			->setChecked($data['rules'][CRoleHelper::UI_DEFAULT_ACCESS])
			->setReadonly($data['readonly'])
	))->addClass(CFormField::ZBX_STYLE_FORM_FIELD_FLUID)
]);

$form_grid->addItem(
	(new CFormField((new CTag('h4', true, _('Access to modules')))->addClass('input-section')))
		->addClass(CFormField::ZBX_STYLE_FORM_FIELD_FLUID)
		->addClass(CFormField::ZBX_STYLE_FORM_FIELD_OFFSET_1)
);

$modules = [];
foreach ($data['labels']['modules'] as $moduleid => $label) {
	$modules[] = new CDiv(
		(new CCheckBox(CRoleHelper::SECTION_MODULES.'['.$moduleid.']', 1))
			->setChecked($data['rules']['modules'][$moduleid])
			->setReadonly($data['readonly'])
			->setLabel($label)
	);
}

if ($modules) {
	$form_grid->addItem([
		(new CFormField(
			(new CDiv(
				(new CDiv($modules))
					->addClass(ZBX_STYLE_COLUMNS)
					->addClass(ZBX_STYLE_COLUMNS_3)
			))->setWidth(ZBX_TEXTAREA_STANDARD_WIDTH)
		))
			->addClass(CFormField::ZBX_STYLE_FORM_FIELD_FLUID)
			->addClass(CFormField::ZBX_STYLE_FORM_FIELD_OFFSET_1)
	]);
}
else {
	$form_grid->addItem(
		(new CFormField((new CLabel(_('No enabled modules found.')))))
			->addClass(CFormField::ZBX_STYLE_FORM_FIELD_FLUID)
			->addClass(CFormField::ZBX_STYLE_FORM_FIELD_OFFSET_1)
	);
}

$form_grid
	->addItem([
		new CLabel(_('Default access to new modules'), 'modules.default_access'),
		(new CFormField(
			(new CCheckBox('modules.default_access', 1))
				->setChecked($data['rules'][CRoleHelper::MODULES_DEFAULT_ACCESS])
				->setReadonly($data['readonly'])
		))->addClass(CFormField::ZBX_STYLE_FORM_FIELD_FLUID)
	])
	->addItem(
		(new CFormField((new CTag('h4', true, _('Access to API')))->addClass('input-section')))
			->addClass(CFormField::ZBX_STYLE_FORM_FIELD_FLUID)
			->addClass(CFormField::ZBX_STYLE_FORM_FIELD_OFFSET_1)
	)
	->addItem([
		new CLabel(_('Enabled'), 'api.access'),
		(new CFormField(
			(new CCheckBox('api.access', 1))
				->setChecked($data['rules'][CRoleHelper::API_ACCESS])
				->setReadonly($data['readonly'])
		))->addClass(CFormField::ZBX_STYLE_FORM_FIELD_FLUID)
	])
	->addItem([
		new CLabel(_('API methods'), 'api.mode'),
		(new CFormField(
			(new CRadioButtonList('api.mode', $data['rules'][CRoleHelper::API_MODE]))
				->addValue(_('Allow list'), '0')
				->addValue(_('Deny list'), '1')
				->setModern(true)
				->setReadonly($data['readonly'])
		))->addClass(CFormField::ZBX_STYLE_FORM_FIELD_FLUID)
	])
	->addItem(
		(new CFormField(
			(new CPatternSelect([
				'name' => 'api_denied[]',
				'object_name' => 'hosts',
				'data' => [],
				'placeholder' => _('API method pattern'),
				'popup' => [
					'parameters' => [
						'srctbl' => 'hosts',
						'srcfld1' => 'host',
						'dstfrm' => $form->getName(),
						'dstfld1' => zbx_formatDomId('api_denied'.'[]')
					]
				],
				// 'add_post_js' => false
			]))->setWidth(ZBX_TEXTAREA_MEDIUM_WIDTH)
		))
			->addClass(CFormField::ZBX_STYLE_FORM_FIELD_FLUID)
			->addClass(CFormField::ZBX_STYLE_FORM_FIELD_OFFSET_1)
	)
	->addItem(
		(new CFormField((new CTag('h4', true, _('Access to actions')))->addClass('input-section')))
			->addClass(CFormField::ZBX_STYLE_FORM_FIELD_FLUID)
			->addClass(CFormField::ZBX_STYLE_FORM_FIELD_OFFSET_1)
	);

$actions = [];
foreach ($data['labels']['actions'] as $action => $label) {
	$actions[] = new CDiv(
		(new CCheckBox($action, 1))
			->setChecked(array_key_exists($action, $data['rules']) && $data['rules'][$action])
			->setReadonly($data['readonly'])
			->setLabel($label)
	);
}

$form_grid->addItem(
	(new CFormField($actions))
		->addClass(CFormField::ZBX_STYLE_FORM_FIELD_FLUID)
		->addClass(CFormField::ZBX_STYLE_FORM_FIELD_OFFSET_1)
);

$form_grid->addItem([
	new CLabel(_('Default access to new actions'), 'actions.default_access'),
	(new CFormField(
		(new CCheckBox('actions.default_access'))
			->setChecked($data['rules'][CRoleHelper::ACTIONS_DEFAULT_ACCESS])
			->setReadonly($data['readonly'])
	))->addClass(CFormField::ZBX_STYLE_FORM_FIELD_FLUID)
]);

$form_grid->addItem(
	(new CFormActions(
		($data['roleid'] != 0)
			? (new CSubmitButton(_('Update'), 'action', 'userrole.update'))
				->setId('update')
				->setEnabled(!$data['readonly'])
			: (new CSubmitButton(_('Add'), 'action', 'userrole.create'))->setId('add'),
		[
			(new CRedirectButton(_('Cancel'),
				(new CUrl('zabbix.php'))
					->setArgument('action', 'userrole.list')
					->setArgument('page', CPagerHelper::loadPage('userrole.list', null))
			))->setId('cancel')
		]
	))
		->addClass(CFormField::ZBX_STYLE_FORM_FIELD_FLUID)
		->addClass(CFormField::ZBX_STYLE_FORM_FIELD_OFFSET_1)
);

$tabs = (new CTabView())->addTab('user_role_tab', _('User role'), $form_grid);

$form->addItem((new CTabView())->addTab('user_role_tab', _('User role'), $form_grid));
$widget->addItem($form);

echo '<style type="text/css">'
. '
.input-section {
	padding-top: 10px;
}
'
.'</style>';

$widget->show();

$this->addJsFile('multiselect.js');
