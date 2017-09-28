/*
** Zabbix
** Copyright (C) 2001-2017 Zabbix SIA
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

#include "zbxtests.h"

int	main(void)
{
	int 	result;

	if (SUCCEED == result)
	{
		/* debug_print_cases(); */

		const struct CMUnitTest tests[] =
		{
			cmocka_unit_test(test_try_task_closes_problem)
			/* cmocka_unit_test(test_process_escalations) */
		};

		result = cmocka_run_group_tests(tests, NULL, NULL);

	}

	free_data();

	return result;
}
