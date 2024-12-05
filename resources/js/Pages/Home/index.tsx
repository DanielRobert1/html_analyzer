import React, { useState } from "react";
import { Head } from "@inertiajs/react";
import styles from "./Home.module.css";
import AccessibilityChart from "../../Components/AccessibilityChart";
import useUploadFile from "../../hooks/useUploadFile";

const Home: React.FC = () => {
    const [file, setFile] = useState<File | null>(null);
    const { upload, loading, error, response } = useUploadFile();

    const handleFileChange = (event: React.ChangeEvent<HTMLInputElement>) => {
        if (event.target.files && event.target.files.length > 0) {
            setFile(event.target.files[0]);
        }
    };

    const handleUpload = async () => {
        if (!file) {
            alert("Please select a file to upload.");
            return;
        }

        await upload(file);
    };

    return (
        <>
            <Head title="Home" />
            <div className={styles.container}>
                <h2 className={styles.header}>Daniel Aigbe File Accessibility Checker</h2>

                <div className={styles.inputWrapper}>
                    <input
                        type="file"
                        onChange={handleFileChange}
                        className={styles.inputFile}
                    />
                    <button
                        onClick={handleUpload}
                        className={styles.button}
                        disabled={loading}
                    >
                        {loading ? "Uploading..." : "Upload File"}
                    </button>
                </div>

                {error && <div className={styles.error}>{error}</div>}

                {response && (
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

                        {response.issues.length > 0 && (
                            <div>
                                <AccessibilityChart issues={response.issues} />
                                <div>
                                    <h4 style={{ marginTop: "20px" }}>
                                        Detailed Issues
                                    </h4>
                                    <ul className={styles.issueList}>
                                        {response.issues.map((issue, index) => (
                                            <li
                                                key={index}
                                                className={styles.issueItem}
                                            >
                                                <div
                                                    className={styles.issueName}
                                                >
                                                    <strong>
                                                        {issue.name}:
                                                    </strong>
                                                </div>
                                                <div
                                                    className={
                                                        styles.issueDescription
                                                    }
                                                >
                                                    {issue.description}
                                                </div>
                                                <ul
                                                    className={
                                                        styles.issueDetails
                                                    }
                                                >
                                                    {issue.details.map(
                                                        (detail, i) => (
                                                            <li
                                                                key={i}
                                                                className={
                                                                    styles.issueDetailItem
                                                                }
                                                            >
                                                                <code
                                                                    className={
                                                                        styles.detailTag
                                                                    }
                                                                >
                                                                    {detail.tag}
                                                                </code>
                                                                :{" "}
                                                                <span
                                                                    className={
                                                                        styles.detailReason
                                                                    }
                                                                >
                                                                    {
                                                                        detail.reason
                                                                    }
                                                                </span>
                                                                <span
                                                                    className={
                                                                        styles.detailSuggestion
                                                                    }
                                                                >
                                                                    {/* Optionally add suggestion if it exists */}
                                                                    {detail.suggestion && (
                                                                        <div
                                                                            className={
                                                                                styles.suggestionText
                                                                            }
                                                                        >
                                                                            <strong>
                                                                                Suggestion:
                                                                            </strong>{" "}
                                                                            {
                                                                                detail.suggestion
                                                                            }
                                                                        </div>
                                                                    )}
                                                                </span>
                                                            </li>
                                                        )
                                                    )}
                                                </ul>
                                            </li>
                                        ))}
                                    </ul>
                                </div>
                            </div>
                        )}
                        {response.issues.length === 0 && (
                            <div className={styles.noIssues}>
                                <p>No issues found.</p>
                            </div>
                        )}
                    </div>
                )}
            </div>
        </>
    );
};

export default Home;
