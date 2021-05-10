/**
 * External dependencies
 */
import { __ } from '@wordpress/i18n';
import { useMemo } from '@wordpress/element';
import { Chart } from '@woocommerce/components';
import { getChartTypeForQuery } from '@woocommerce/date';

/**
 * Internal dependencies
 */
import useUrlQuery from '.~/hooks/useUrlQuery';

const emptyMessage = __(
	'No data for the selected date range',
	'google-listings-and-ads'
);

/**
 * Renders a report chart.
 *
 * @param {Object} props React props.
 * @param {Array<Metric>} props.metrics Metrics to display.
 * @param {ProductsReportSchema} props.report Report data and its status.
 */
export default function ChartSection( { metrics, report } ) {
	const query = useUrlQuery();
	const { orderby } = query;
	const { key, label } = orderby
		? metrics.find( ( metric ) => metric.key === orderby )
		: metrics[ 0 ];

	const chartType = getChartTypeForQuery( query );

	const {
		loaded,
		data: { intervals },
	} = report;

	const chartData = useMemo( () => {
		if ( ! loaded ) {
			return [];
		}

		return intervals.map( ( { interval, subtotals } ) => {
			return {
				date: interval,
				[ label ]: {
					value: subtotals[ key ],
					label,
				},
			};
		} );
	}, [ key, label, loaded, intervals ] );

	return (
		<Chart
			data={ chartData }
			title={ label }
			query={ query }
			chartType={ chartType }
			isRequesting={ ! loaded }
			emptyMessage={ emptyMessage }
			layout="time-comparison"
			// 'hidden' is NOT a valid `legendPosition` value, but it can hack to hide the legend.
			legendPosition="hidden"
		/>
	);
}

/**
 * @typedef {import("./index.js").Metric} Metric
 * @typedef {import("./index.js").ProductsReportSchema} ProductsReportSchema
 */
