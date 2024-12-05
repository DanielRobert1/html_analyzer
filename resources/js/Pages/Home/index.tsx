import { Head, usePage } from "@inertiajs/react";
import React, { useState } from "react";
import axios from "axios";

type Issue = {
    type: string;
    description: string;
};

type ResponseData = {
    score: number;
    issues: Issue[];
    suggestions: string[];
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
                "/api/upload",
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

    return (
        <>
            <Head title="Home" />
            <div
                style={{
                    maxWidth: "600px",
                    margin: "50px auto",
                    padding: "20px",
                    border: "1px solid #ddd",
                    borderRadius: "8px",
                }}
            >
                <h2 style={{ textAlign: "center" }}>
                    File Accessibility Checker
                </h2>

                <div style={{ marginBottom: "20px" }}>
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
                        }}
                    >
                        <h3>Accessibility Report</h3>
                        <p>
                            <strong>Score:</strong> {response.score}
                        </p>
                        <h4>Issues</h4>
                        <ul>
                            {response.issues.map((issue, index) => (
                                <li key={index}>
                                    <strong>{issue.type}:</strong>{" "}
                                    {issue.description}
                                </li>
                            ))}
                        </ul>
                        <h4>Suggestions</h4>
                        <ul>
                            {response.suggestions.map((suggestion, index) => (
                                <li key={index}>{suggestion}</li>
                            ))}
                        </ul>
                    </div>
                )}
            </div>
        </>
    );
}
