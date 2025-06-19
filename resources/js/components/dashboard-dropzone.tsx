'use client';

import { toast } from '@/hooks/use-toast';
import { UploadValidationRequest, uploadValidation } from '@/lib/validators/uploadValidation';
import { useMutation } from '@tanstack/react-query';
import axios, { AxiosError } from 'axios';
import Papa from 'papaparse';
import { useCallback, useEffect, useRef, useState } from 'react';
import { Button } from './ui/button';
import DropZone from './ui/dropzone';
import { Input } from './ui/input';

export default function DashboardDropzone() {
    const [csvData, setCsvData] = useState<UploadValidationRequest>([]);
    const [disable, setDisabled] = useState<boolean>(true);
    const clickRef = useRef<null | HTMLDivElement>(null);
    const inputRef = useRef<null | HTMLInputElement>(null);

    // HANDLE FILE UPLOAD
    function handleFiles(event: any) {
        event.preventDefault();
        if (event.target.files) {
            setDisabled(false);
            onFilesDrop?.(event.target.files);
        }
    }

    function onSubmit() {
        const payload = csvData;
        createListing(payload);
    }

    const { mutate: createListing } = useMutation({
        mutationFn: async (payload: UploadValidationRequest) => {
            console.log('payload', payload);

            const response = await axios.post('/upload-csv', payload);
            console.log('response: ', response);
        },
        onError: (error: AxiosError) => {
            setCsvData([]);
            setDisabled(true);
            if (error.response?.status === 400) {
                return toast({
                    title: 'Bad Request.',
                    description: `Data validation error, only 1 file is allowed.`,
                    variant: 'destructive',
                });
            }
            if (error.response?.status === 429) {
                return toast({
                    title: 'Limit reached.',
                    description: `API request limit reached.`,
                    variant: 'destructive',
                });
            }
            if (error.response?.status === 465) {
                return toast({
                    title: 'Transaction failed.',
                    description: `Transaction failed, either due to a LLM connection error or insufficient funds.`,
                    variant: 'destructive',
                });
            }
            if (error.response?.status === 500) {
                return toast({
                    title: 'Server Error.',
                    description: 'Failed to complete operation due to a server error. Please try again.',
                    variant: 'destructive',
                });
            }
        },
        onSuccess: () => {
            setCsvData([]);
            setDisabled(true);
            return toast({
                title: 'Success!',
                description: 'Successfully uploaded to the network.',
            });
        },
        onSettled: async (_, error) => {
            if (error) {
                console.log('onSettled error:', error);
            }
        },
    });

    // DROPZONE
    const [isDropActive, setIsDropActive] = useState<boolean>(false);

    const onDragStateChange = useCallback((dragActive: boolean) => {
        setIsDropActive(dragActive);
    }, []);

    const onFilesDrop = useCallback(async (files: File[]) => {
        Papa.parse(files[0], {
            delimiter: ';',
            skipEmptyLines: true,
            complete: function (results) {
                const data = results.data.map((row: any) => ({
                    title: row[0],
                    description: row[1],
                    plantCategory: row[2],
                    contactEmail: row[3],
                    phoneNumber: row[4],
                    website: row[5],
                    hireRatePricing: row[6],
                    tags: row[7],
                    companyLogo: row[8],
                    photoGallery: row[9],
                    attachments: row[10],
                    socialNetworks: row[11],
                    location: row[12],
                    region: row[13],
                    relatedListing: row[14],
                    hireRental: row[15],
                }));
                const validatedData = uploadValidation.parse(data)
                setCsvData(validatedData);
                setDisabled(false);
            },
        });
    }, []);

    function handleClick() {
        document.getElementById('input')?.click();
    }

    function clear() {
        setCsvData([]);
        return toast({
            title: 'Cleared',
            description: 'Dropzone has been cleared.',
        });
    }

    useEffect(() => {
        const currentRef = clickRef.current;
        if (currentRef) {
            currentRef.addEventListener('click', handleClick);
            return () => {
                currentRef.removeEventListener('click', handleClick);
            };
        }
    }, []);

    return (
        <div className="flex min-h-screen w-full flex-col items-center py-5 md:py-10">
            <DropZone onDragStateChange={onDragStateChange} onFilesDrop={onFilesDrop}>
                <div
                    ref={clickRef}
                    className="absolute top-2 left-2 flex h-8 w-28 items-center justify-center rounded-full border border-muted-foreground bg-muted text-center transition duration-200 hover:scale-[0.97] hover:border-blue-500"
                >
                    <p className="cursor-pointer text-primary">Browse..</p>
                    <Input
                        id="input"
                        ref={inputRef}
                        hidden
                        type="file"
                        accept='.csv'
                        className="absolute top-0 left-0 h-0 w-0 border-none bg-transparent text-transparent"
                        onChangeCapture={handleFiles}
                    ></Input>
                </div>
                <h2 className="text-2xl font-semibold">Drop CSV Here</h2>
                {csvData && csvData.length > 0 ? (
                    <p className="flex h-8 w-60 items-center justify-center gap-2 rounded-full border-transparent text-center font-semibold italic">
                        <span>{csvData?.length} entries parsed</span>
                    </p>
                ) : (
                    <p className="flex h-8 w-60 items-center justify-center rounded-full border-transparent text-center text-muted-foreground italic">
                        No file selected
                    </p>
                )}
            </DropZone>
            <div className="mt-5 flex gap-5">
                <Button
                    id="submitButton"
                    disabled={disable}
                    onClick={onSubmit}
                >
                    Submit
                </Button>
                <Button variant="secondary" className="" onClick={clear}>
                    Clear
                </Button>
            </div>
        </div>
    );
}
