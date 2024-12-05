import axios from 'axios';
import { ResponseData } from '../types';

const UPLOAD_API_URL = '/api/accessibility-analyze';

export const uploadFile = async (file: File): Promise<ResponseData> => {
    const formData = new FormData();
    formData.append('file', file);

    try {
        const response = await axios.post<ResponseData>(UPLOAD_API_URL, formData, {
            headers: { 'Content-Type': 'multipart/form-data' },
        });
        return response.data;
    } catch (error: any) {
        throw new Error(error.response?.data?.message || 'An error occurred while uploading the file.');
    }
};
