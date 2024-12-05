import React, { useState } from "react";
import { Head } from "@inertiajs/react";
import styles from "./Home.module.css";
import useUploadFile from "../../hooks/useUploadFile";
import AccessibilityReport from "../../Components/AccessibilityReport";
import FileUpload from "../../Components/FileUpload";

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

                <FileUpload
                    handleFileChange={handleFileChange}
                    handleUpload={handleUpload}
                    loading={loading}
                />

                {error && <div className={styles.error}>{error}</div>}

                {response && (
                    <AccessibilityReport response={response} />
                )}
            </div>
        </>
    );
};

export default Home;
