'use client';

import Folder from '@/assets/FOLDER.png';
import { toast } from '@/hooks/use-toast';
import { cn } from '@/lib/utils';
import { UploadValidationRequest, uploadValidation } from '@/lib/validators/uploadValidation';
import { ValidationError } from '@/types/zod-error';
import { useMutation } from '@tanstack/react-query';
import axios, { AxiosError } from 'axios';
import { AlertTriangle, Check, FileSearch, Loader2, X } from 'lucide-react';
import Papa from 'papaparse';
import { useCallback, useEffect, useRef, useState } from 'react';
import { ZodError } from 'zod';
import CsvValidationErrors from './csv-validation-errors';
import { Button } from './ui/button';
import DropZone from './ui/dropzone';
import { Input } from './ui/input';

export default function DashboardDropzone() {
    const [csvData, setCsvData] = useState<UploadValidationRequest>([]);
    const [disable, setDisabled] = useState<boolean>(true);
    const [uploadStatus, setUploadStatus] = useState<string>('WAITING');
    const [uploadSuccess, setUploadSuccess] = useState<boolean>(false);
    const [uploadFailed, setUploadFailed] = useState<boolean>(false);
    const [validationErrors, setValidationErrors] = useState<ValidationError[]>([]);
    const clickRef = useRef<null | HTMLDivElement>(null);
    const inputRef = useRef<null | HTMLInputElement>(null);
    const dataStages = ['WAITING', 'VALID', 'INVALID'];

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

    const {
        mutate: createListing,
        isPending,
        isSuccess,
    } = useMutation({
        mutationFn: async (payload: UploadValidationRequest) => {
            setDisabled(true);
            await axios.post('V2/listings', payload);
        },
        onError: (error: AxiosError) => {
            setUploadFailed(true);
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
            skipFirstNLines: 1,
            skipEmptyLines: true,
            complete: function (results) {
                const data = results.data.map((row: any) => ({
                    title: row[0],
                    description: row[1],
                    plant_category: row[2],
                    company_name: row[3],
                    contact_email: row[4],
                    phone_number: `+${row[5]}`,
                    website: row[6],
                    hire_rate_gbp: row[7],
                    hire_rate_eur: row[8],
                    hire_rate_usd: row[9],
                    hire_rate_aud: row[10],
                    hire_rate_nzd: row[11],
                    hire_rate_zar: row[12],
                    tags: row[13].split(',').map((item: string) => {
                        return item.trim();
                    }),
                    company_logo: row[14].split(',').map((item: string) => {
                        return item.trim();
                    }),
                    photo_gallery: row[15].split(',').map((item: string) => {
                        return item.trim();
                    }),
                    attachments: row[16].split(',').map((item: string) => {
                        return item.trim();
                    }),
                    social_networks: row[17].split(',').map((item: string) => {
                        return item.trim();
                    }),
                    location: row[18],
                    latitude: parseFloat(row[19]),
                    longitude: parseFloat(row[20]),
                    region: row[21],
                    related_listing: row[22].split(',').map((item: string) => {
                        return parseFloat(item.trim());
                    }),
                    hire_rental: row[23],
                    additional_1: row[24],
                    additional_2: row[25],
                    additional_3: row[26],
                    additional_4: row[27],
                    additional_5: row[28],
                    additional_6: row[29],
                    additional_7: row[30],
                    additional_8: row[31],
                    additional_9: row[32],
                    additional_10: row[33],
                }));

                try {
                    const validatedData = uploadValidation.parse(data);
                    setCsvData(validatedData);
                    setUploadStatus(dataStages[1]);
                    setDisabled(false);
                } catch (error) {
                    if (error instanceof ZodError) {
                        setUploadStatus(dataStages[2]);
                        const formattedErrors = error.errors.map((e) => ({
                            path: e.path.join('.'),
                            message: e.message,
                        }));

                        setValidationErrors(formattedErrors);

                        console.error('Validation Errors:', formattedErrors);

                        toast({
                            title: 'Validation Error',
                            description: `Found ${formattedErrors.length} error(s). Please fix them in your CSV.`,
                            variant: 'destructive',
                        });
                    }
                }
            },
        });
    }, []);

    function handleClick() {
        document.getElementById('input')?.click();
    }

    function clear() {
        setUploadStatus(dataStages[0]);
        setUploadSuccess(false);
        setUploadFailed(false);
        setValidationErrors([]);
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
            <div className="mb-5 flex w-2/3 min-w-[300px] flex-col gap-3 text-sm sm:flex-row">
                {/* STEP 1 */}
                <div
                    className={cn(
                        'flex w-full flex-row items-center justify-between rounded-lg border border-muted p-2 text-muted-foreground/50 shadow-md',
                        uploadStatus === dataStages[1] && 'text-primary',
                    )}
                >
                    <div className="flex items-center justify-start gap-5">
                        <div
                            className={cn(
                                'flex size-6 items-center justify-center rounded-full bg-muted',
                                uploadStatus === dataStages[1] && 'bg-green-500 text-white',
                                uploadStatus === dataStages[2] && 'bg-red-500 text-white',
                            )}
                        >
                            <p>1</p>
                        </div>
                        <p className="truncate">Processed</p>
                    </div>
                    <div className="flex items-center justify-center">
                        {uploadStatus === dataStages[0] && <Check className="text-muted" />}
                        {uploadStatus === dataStages[1] && <Check className="text-green-500" />}
                        {uploadStatus === dataStages[2] && <X className="text-red-500" />}
                    </div>
                </div>

                {/* STEP 2 */}
                <div
                    className={cn(
                        'flex w-full flex-row items-center justify-between rounded-lg border border-muted p-2 text-muted-foreground/50 shadow-md',
                        uploadStatus === dataStages[1] && 'text-primary',
                    )}
                >
                    <div className="flex items-center justify-start gap-5">
                        <div
                            className={cn(
                                'flex size-6 items-center justify-center rounded-full bg-muted',
                                uploadStatus === dataStages[1] && 'bg-green-500 text-white',
                                uploadStatus === dataStages[2] && 'bg-orange-500 text-white',
                            )}
                        >
                            <p>2</p>
                        </div>
                        <p className="truncate">Valid</p>
                    </div>
                    <div className="flex items-center justify-center">
                        {uploadStatus === dataStages[0] && <Check className="text-muted" />}
                        {uploadStatus === dataStages[1] && <Check className="text-green-500" />}
                        {uploadStatus === dataStages[2] && <AlertTriangle className="text-orange-500" />}
                    </div>
                </div>

                {/* STEP 3 */}
                <div
                    className={cn(
                        'flex w-full flex-row items-center justify-between rounded-lg border border-muted p-2 text-muted-foreground/50 shadow-md',
                        uploadSuccess && 'text-primary',
                        isPending && 'text-primary',
                    )}
                >
                    <div className="flex items-center justify-start gap-5">
                        <div
                            className={cn(
                                'flex size-6 items-center justify-center rounded-full bg-muted',
                                uploadSuccess && 'bg-green-500 text-white',
                                uploadFailed && 'bg-red-500 text-white',
                            )}
                        >
                            <p>3</p>
                        </div>
                        {uploadFailed ? <p className="truncate text-primary">Failed</p> : <p className="truncate">Complete</p>}
                    </div>
                    <div className="flex items-center justify-center">
                        {uploadFailed ? (
                            <X className="text-red-500" />
                        ) : isPending ? (
                            <Loader2 className="animate-spin" />
                        ) : (
                            <Check className={cn('text-muted', uploadSuccess && 'text-green-500')} />
                        )}
                    </div>
                </div>
            </div>
            <DropZone onDragStateChange={onDragStateChange} onFilesDrop={onFilesDrop}>
                {isPending ? (
                    <div className="flex size-full flex-col items-center justify-center">
                        <Loader2 className="size-20 animate-spin text-muted-foreground" />
                    </div>
                ) : (
                    <>
                        <img src={Folder} alt="csv-folder" className="w-20" />
                        <h2 className="text-center text-lg font-semibold">Choose a csv file or drag and drop it here</h2>
                        <div
                            ref={clickRef}
                            className="flex h-10 cursor-pointer items-center justify-center gap-3 rounded-md border border-muted-foreground/50 bg-muted p-3 text-center shadow-md transition duration-200 hover:scale-[0.97] hover:border-blue-500"
                        >
                            <FileSearch />
                            <p>Browse..</p>
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
                        {validationErrors.length > 0 && <CsvValidationErrors errors={validationErrors} />}
                        {csvData && csvData.length > 0 && (
                            <p className="absolute bottom-3 mx-auto flex h-8 w-60 items-center justify-center gap-2 rounded-full border-transparent text-center text-muted-foreground">
                                <span>{csvData?.length} entries parsed</span>
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
