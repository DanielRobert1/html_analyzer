import { useState } from 'react';
import { uploadFile } from '../services/apiService';
import { ResponseData } from '../types';

const useUploadFile = () => {
    const [loading, setLoading] = useState(false);
    const [error, setError] = useState<string | null>(null);
    const [response, setResponse] = useState<ResponseData | null>(null);

    const upload = async (file: File) => {
        setLoading(true);
        setError(null);

        try {
            const result = await uploadFile(file);
            setResponse(result);
        } catch (err: any) {
            setError(err.message || 'An error occurred while uploading the file.');
        } finally {
            setLoading(false);
        }
    };

    return {
        upload,
        loading,
        error,
        response,
    };
};

export default useUploadFile;
