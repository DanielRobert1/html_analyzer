export interface IssueDetail {
    tag: string;
    reason: string;
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
