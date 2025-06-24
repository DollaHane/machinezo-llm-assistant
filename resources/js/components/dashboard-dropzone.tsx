'use client';

import { toast } from '@/hooks/use-toast';
import { UploadValidationRequest, uploadValidation } from '@/lib/validators/uploadValidation';
import { useMutation } from '@tanstack/react-query';
import axios, { AxiosError } from 'axios';
import { Loader2 } from 'lucide-react';
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
        return toast({
            title: 'Uploading',
            description: 'Processing data and generating content',
        });
    }

    const { mutate: createListing, isPending } = useMutation({
        mutationFn: async (payload: UploadValidationRequest) => {
            setDisabled(true);
            await axios.post('V2/listings', payload);
        },
        onError: (error: AxiosError) => {
            setCsvData([]);
            if (error.response?.status === 400) {
                return toast({
                    title: 'Bad Request.',
                    description: `Data validation error, please check the data integrity of your CSV file.`,
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
                    title: 'Content generation failed.',
                    description: `Content generation failed, either due to a LLM connection error or insufficient funds.`,
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
                    plant_category: row[2],
                    contact_email: row[3],
                    phone_number: row[4],
                    website: row[5],
                    hire_rate_gbp: row[6],
                    hire_rate_eur: row[7],
                    hire_rate_usd: row[8],
                    hire_rate_aud: row[9],
                    hire_rate_nzd: row[10],
                    hire_rate_zar: row[11],
                    tags: row[12].split(',').map((item: string) => {
                        return item.trim();
                    }),
                    company_logo: row[13],
                    photo_gallery: row[14].split(',').map((item: string) => {
                        return item.trim();
                    }),
                    attachments: row[15].split(',').map((item: string) => {
                        return item.trim();
                    }),
                    social_networks: row[16].split(',').map((item: string) => {
                        return item.trim();
                    }),
                    location: row[17],
                    region: row[18],
                    related_listing: row[19].split(',').map((item: string) => {
                        return item.trim();
                    }),
                    hire_rental: row[20],
                    additional_1: row[21],
                    additional_2: row[22],
                    additional_3: row[23],
                    additional_4: row[24],
                    additional_5: row[25],
                    additional_6: row[26],
                    additional_7: row[27],
                    additional_8: row[28],
                    additional_9: row[29],
                    additional_10: row[30],
                }));
                const validatedData = uploadValidation.parse(data);
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
                {isPending ? (
                    <div className="flex size-full flex-col items-center justify-center">
                        <Loader2 className="size-16 animate-spin" />
                        <p className="mt-5 animate-pulse text-muted-foreground">Processing data and generating content.</p>
                    </div>
                ) : (
                    <>
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
                                accept=".csv"
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
                    </>
                )}
            </DropZone>
            <div className="mt-5 flex gap-5">
                <Button id="submitButton" disabled={disable} onClick={onSubmit}>
                    Submit
                </Button>
                <Button variant="secondary" className="" onClick={clear}>
                    Clear
                </Button>
            </div>
        </div>
    );
}
