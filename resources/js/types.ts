export interface IssueDetail {
    tag: string;
    reason: string;
    suggestion: string;
}

export type Issue = {
    name: string;
    description: string;
    count: number;
    details: IssueDetail[];
};

export type ResponseData = {
    score: number;
    issues: Issue[];
};

export interface FileUploadProps {
    handleFileChange: (event: React.ChangeEvent<HTMLInputElement>) => void;
    handleUpload: () => void;
    loading: boolean;
}

export interface AccessibilityReportProps {
    response: ResponseData;
}
