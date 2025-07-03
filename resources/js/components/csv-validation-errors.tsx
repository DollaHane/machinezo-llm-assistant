import { ValidationError } from '@/types/zod-error';
import { Sheet, SheetContent, SheetHeader, SheetTitle, SheetTrigger } from './ui/sheet';

interface CsvValidationErrorsProps {
    errors: ValidationError[];
}

export default function CsvValidationErrors({ errors }: CsvValidationErrorsProps) {
    return (
        <Sheet>
            <SheetTrigger className="absolute top-5 right-5 cursor-pointer rounded-md border border-red-500 p-2 px-3 text-xs shadow-md shadow-red-500/50">
                See errors
            </SheetTrigger>
            <SheetContent>
                <SheetHeader>
                    <SheetTitle>Validation Errors</SheetTitle>
                </SheetHeader>
                <div className="flex flex-col gap-5 p-5 text-xs">
                    {errors.map((error) => (
                        <div>
                            <div className="flex flex-row gap-3">
                                <p className="w-16 font-semibold">Field:</p>
                                <p className="text-muted-foreground">{error.path}</p>
                            </div>
                            <div className="flex flex-row gap-3">
                                <p className="w-16 font-semibold">Message: </p>
                                <p className="text-muted-foreground">{error.message}</p>
                            </div>
                        </div>
                    ))}
                </div>
            </SheetContent>
        </Sheet>
    );
}
