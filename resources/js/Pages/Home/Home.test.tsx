import React from "react";
import { render, screen, fireEvent, waitFor } from "@testing-library/react";
import '@testing-library/jest-dom';
import Home from "./";
import useUploadFile from "../../hooks/useUploadFile";

// Mock the useUploadFile hook
jest.mock("../../hooks/useUploadFile", () => ({
    __esModule: true,
    default: jest.fn(),
}));

// Mock the Head component from Inertia.js
jest.mock("@inertiajs/react", () => ({
    Head: () => <></>, // Mock Head as a functional component
}));

describe("Home Component", () => {
    const mockUpload = jest.fn();
    const mockUseUploadFile = {
        upload: mockUpload,
        loading: false,
        error: null,
        response: null,
    };

    beforeEach(() => {
        (useUploadFile as jest.Mock).mockReturnValue(mockUseUploadFile);
        jest.clearAllMocks();
    });

    test("renders the component correctly", () => {
        render(<Home />);
        expect(screen.getByText("Daniel Aigbe File Accessibility Checker")).toBeInTheDocument();
        expect(screen.getByRole("button", { name: /upload/i })).toBeInTheDocument();
    });

    test("displays an alert if trying to upload without selecting a file", () => {
        // Type-cast window.alert as a jest.Mock
        const alertMock = jest.spyOn(window, "alert").mockImplementation(() => {});

        render(<Home />);
        const uploadButton = screen.getByRole("button", { name: /upload/i });

        fireEvent.click(uploadButton);

        expect(alertMock).toHaveBeenCalledWith("Please select a file to upload.");

        // Restore alert after the test
        alertMock.mockRestore();
      });

    test("calls the upload function when a file is selected and upload is clicked", async () => {
        render(<Home />);

        const fileInput = screen.getByRole("file"); // Adjust label text based on your implementation
        const file = new File(["file content"], "test.html", { type: "text/html" });

        fireEvent.change(fileInput, { target: { files: [file] } });

        const uploadButton = screen.getByRole("button", { name: /upload/i });
        fireEvent.click(uploadButton);

        await waitFor(() => {
            expect(mockUpload).toHaveBeenCalledWith(file);
        });
    });

    test("displays an error message if the upload fails", () => {
        (useUploadFile as jest.Mock).mockReturnValue({
            ...mockUseUploadFile,
            error: "Upload failed",
        });

        render(<Home />);
        expect(screen.getByText("Upload failed")).toBeInTheDocument();
    });

    test("renders the AccessibilityReport component when there is a response", () => {
        (useUploadFile as jest.Mock).mockReturnValue({
            ...mockUseUploadFile,
            response: { score: 95, issues: [] },
        });

        render(<Home />);
        expect(screen.getByText("Accessibility Report")).toBeInTheDocument(); // Adjust based on your component's rendering
    });
});
