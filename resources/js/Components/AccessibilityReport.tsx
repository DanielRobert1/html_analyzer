import React from "react";
import styles from "../Pages/Home/Home.module.css";
import AccessibilityChart from "./AccessibilityChart";
import { AccessibilityReportProps, ResponseData } from "../types";



const AccessibilityReport: React.FC<AccessibilityReportProps> = ({ response }) => (
    <div className={styles.reportWrapper}>
        <h3 className={styles.header}>Accessibility Report</h3>
        <div className={styles.scoreWrapper}>
            <p>
                <strong>Score:</strong> {response.score}
            </p>
            <div className={styles.progressBarWrapper}>
                <div
                    className={styles.progressBar}
                    style={{ width: `${response.score}%` }}
                />
            </div>
        </div>

        {response.issues.length > 0 ? (
            <div>
                <AccessibilityChart issues={response.issues} />
                <h4 style={{ marginTop: "20px" }}>Detailed Issues</h4>
                <ul className={styles.issueList}>
                    {response.issues.map((issue, index) => (
                        <li key={index} className={styles.issueItem}>
                            <div className={styles.issueName}>
                                <strong>{issue.name}:</strong>
                            </div>
                            <div className={styles.issueDescription}>
                                {issue.description}
                            </div>
                            <ul className={styles.issueDetails}>
                                {issue.details.map((detail, i) => (
                                    <li key={i} className={styles.issueDetailItem}>
                                        <code className={styles.detailTag}>{detail.tag}</code>:{" "}
                                        <span className={styles.detailReason}>{detail.reason}</span>
                                        {detail.suggestion && (
                                            <div className={styles.suggestionText}>
                                                <strong>Suggestion:</strong> {detail.suggestion}
                                            </div>
                                        )}
                                    </li>
                                ))}
                            </ul>
                        </li>
                    ))}
                </ul>
            </div>
        ) : (
            <div className={styles.noIssues}>
                <p>No issues found.</p>
            </div>
        )}
    </div>
);

export default AccessibilityReport;
