<?php declare(strict_types = 0);
/*
** Zabbix
** Copyright (C) 2001-2022 Zabbix SIA
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
 * Plain text widget form.
 */
class CWidgetFormPlainText extends CWidgetForm {

	public function __construct(array $values, ?string $templateid) {
		parent::__construct(WIDGET_PLAIN_TEXT, $values, $templateid);
	}

	protected function addFields(): self {
		parent::addFields();

		return $this
			->addField(
				(new CWidgetFieldMultiSelectItem('itemids', _('Items'), $this->templateid))
					->setFlags(CWidgetField::FLAG_NOT_EMPTY | CWidgetField::FLAG_LABEL_ASTERISK)
			)
			->addField(
				(new CWidgetFieldRadioButtonList('style', _('Items location'), [
					STYLE_LEFT => _('Left'),
					STYLE_TOP => _('Top')
				]))->setDefault(STYLE_LEFT)
			)
			->addField(
				(new CWidgetFieldIntegerBox('show_lines', _('Show lines'), ZBX_MIN_WIDGET_LINES,
					ZBX_MAX_WIDGET_LINES
				))
					->setDefault(ZBX_DEFAULT_WIDGET_LINES)
					->setFlags(CWidgetField::FLAG_LABEL_ASTERISK)
			)
			->addField(
				new CWidgetFieldCheckBox('show_as_html', _('Show text as HTML'))
			)
			->addField($this->templateid === null
				? new CWidgetFieldCheckBox('dynamic', _('Dynamic items'))
				: null
			);
	}
}
