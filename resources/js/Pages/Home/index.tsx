import { Head } from "@inertiajs/react";
import React, { useState } from "react";
import axios from "axios";
import { Chart } from 'react-google-charts';

interface IssueDetail {
    tag: string;
    reason: string;
}

type Issue = {
    name: string;
    description: string;
    count: number;
    details: IssueDetail[];
};

type ResponseData = {
    score: number;
    issues: Issue[];
};

export default function Home() {
    const [file, setFile] = useState<File | null>(null);
    const [response, setResponse] = useState<ResponseData | null>(null);
    const [loading, setLoading] = useState<boolean>(false);
    const [error, setError] = useState<string | null>(null);

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

        setLoading(true);
        setError(null);

        const formData = new FormData();
        formData.append("file", file);

        try {
            const res = await axios.post<ResponseData>(
                "/api/accessibility-analyze",
                formData,
                {
                    headers: {
                        "Content-Type": "multipart/form-data",
                    },
                }
            );
            setResponse(res.data);
        } catch (err: any) {
            setError(
                err.response?.data?.message ||
                    "An error occurred while uploading the file."
            );
        } finally {
            setLoading(false);
        }
    };

    const getChartData = () => {
        if (!response) return [];

        const data = [
            ["Issue", "Count"],
            ...response.issues.map(issue => [issue.name, issue.count]),
        ];
        return data;
    };

    return (
        <>
            <Head title="Home" />
            <div
                style={{
                    maxWidth: "800px",
                    margin: "50px auto",
                    padding: "20px",
                    border: "1px solid #ddd",
                    borderRadius: "8px",
                    boxShadow: "0 4px 8px rgba(0, 0, 0, 0.1)",
                }}
            >
                <h2 style={{ textAlign: "center", color: "#007BFF" }}>
                    File Accessibility Checker
                </h2>

                <div
                    style={{
                        marginBottom: "20px",
                        transition: "all 0.3s ease",
                    }}
                >
                    <input
                        type="file"
                        onChange={handleFileChange}
                        style={{
                            width: "100%",
                            padding: "10px",
                            marginBottom: "10px",
                            border: "1px solid #ccc",
                            borderRadius: "4px",
                        }}
                    />
                    <button
                        onClick={handleUpload}
                        style={{
                            width: "100%",
                            padding: "10px",
                            backgroundColor: "#007BFF",
                            color: "#fff",
                            border: "none",
                            borderRadius: "4px",
                            cursor: "pointer",
                            fontWeight: "bold",
                        }}
                        disabled={loading}
                    >
                        {loading ? "Uploading..." : "Upload File"}
                    </button>
                </div>

                {error && (
                    <div
                        style={{
                            color: "red",
                            textAlign: "center",
                            marginBottom: "20px",
                        }}
                    >
                        {error}
                    </div>
                )}

                {response && (
                    <div
                        style={{
                            padding: "10px",
                            border: "1px solid #ccc",
                            borderRadius: "4px",
                            backgroundColor: "#f9f9f9",
                            marginTop: "20px",
                            transition: "all 0.3s ease",
                        }}
                    >
                        <h3 style={{ color: "#007BFF" }}>Accessibility Report</h3>
                        <p>
                            <strong>Score:</strong> {response.score}
                        </p>

                        <Chart
                            chartType="PieChart"
                            data={getChartData()}
                            options={{
                                title: "Accessibility Issues",
                                pieHole: 0.4,
                                slices: {
                                    0: { color: "#f54242" },
                                    1: { color: "#f5a742" },
                                },
                            }}
                            width="100%"
                            height="400px"
                        />

                        <h4 style={{ marginTop: "20px" }}>Detailed Issues</h4>
                        <ul style={{ paddingLeft: "20px" }}>
                            {response.issues.map((issue, index) => (
                                <li key={index} style={{ marginBottom: "10px" }}>
                                    <strong>{issue.name}:</strong> {issue.description}
                                    <ul style={{ marginTop: "5px", paddingLeft: "20px" }}>
                                        {issue.details.map((detail, i) => (
                                            <li key={i}>
                                                <code>{detail.tag}</code>: {detail.reason}
                                            </li>
                                        ))}
                                    </ul>
                                </li>
                            ))}
                        </ul>
                    </div>
                )}
            </div>
        </>
    );
}
