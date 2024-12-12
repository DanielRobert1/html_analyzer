import React from "react";
import styles from "../Pages/Home/Home.module.css";
import { FileUploadProps } from "../types";

const FileUpload: React.FC<FileUploadProps> = ({ handleFileChange, handleUpload, loading }) => (
    <div className={styles.inputWrapper}>
        <input
            type="file"
            onChange={handleFileChange}
            role="file"
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
);

export default FileUpload;
