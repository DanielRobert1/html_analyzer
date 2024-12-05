import React from 'react';
import { Chart } from 'react-google-charts';
import { Issue } from '../types'; // Assuming Issue type is in a separate file

interface AccessibilityChartProps {
    issues: Issue[];
}

const AccessibilityChart: React.FC<AccessibilityChartProps> = ({ issues }) => {
    const getChartData = () => {
        return [
            ["Issue", "Count"],
            ...issues.map(issue => [issue.name, issue.count]),
        ];
    };

    const getChartColor = (index: number): string => {
        const colors = [
            "#FF5733", "#FFBD33", "#75FF33", "#33FF57", "#33FFBD",
            "#3357FF", "#5733FF", "#FF33BD", "#FF33A1", "#BD33FF"
        ];
        return colors[index % colors.length];
    };

    return (
        <Chart
            chartType="PieChart"
            data={getChartData()}
            options={{
                title: "Accessibility Issues",
                pieHole: 0.4,
                slices: Object.fromEntries(
                    issues.map((_, index) => [
                        index,
                        { color: getChartColor(index) }
                    ])
                ),
            }}
            width="100%"
            height="400px"
        />
    );
};

export default AccessibilityChart;
