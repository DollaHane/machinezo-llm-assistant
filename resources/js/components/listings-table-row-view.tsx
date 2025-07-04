import {
    AlertDialog,
    AlertDialogCancel,
    AlertDialogContent,
    AlertDialogDescription,
    AlertDialogFooter,
    AlertDialogHeader,
    AlertDialogTitle,
    AlertDialogTrigger,
} from '@/components/ui/alert-dialog';
import { Button } from '@/components/ui/button';
import { Listing } from '@/types/listings';
import { ScrollArea } from './ui/scroll-area';

interface ListingsTableRowViewProps {
    row: Listing;
}

const divStyle = 'w-full flex flex-col mb-5';
const titleStyle = 'text-primary font-semibold mt-5 mb-2';
const contentStyle = 'text-primary text-sm';

export default function ListingsTableRowView({ row }: ListingsTableRowViewProps) {
    console.log('row', row);

    return (
        <AlertDialog>
            <AlertDialogTrigger asChild>
                <Button variant="ghost" className="w-full">
                    Show More
                </Button>
            </AlertDialogTrigger>
            <AlertDialogContent>
                <AlertDialogHeader>
                    <AlertDialogTitle>{row.title}</AlertDialogTitle>
                    <hr />
                    <AlertDialogDescription></AlertDialogDescription>
                    <ScrollArea className="h-[70vh] w-full pr-5">
                        <div className={`${divStyle}`}>
                            <span className={`${titleStyle}`}>Description:</span>
                            <span className={`${contentStyle}`}>{row.description}</span>
                        </div>

                        <div className={`${divStyle}`}>
                            <hr />
                            <span className={`${titleStyle}`}>Plant Category:</span>
                            <span className={`${contentStyle}`}>{row.plant_category}</span>
                        </div>

                        <div className={`${divStyle}`}>
                            <hr />
                            <span className={`${titleStyle}`}>Company Name:</span>
                            <span className={`${contentStyle}`}>{row.company_name}</span>
                        </div>

                        <div className={`${divStyle}`}>
                            <hr />
                            <span className={`${titleStyle}`}>Contact Email:</span>
                            <span className={`${contentStyle}`}>{row.contact_email}</span>
                        </div>

                        <div className={`${divStyle}`}>
                            <hr />
                            <span className={`${titleStyle}`}>Phone Number:</span>
                            <span className={`${contentStyle}`}>{row.phone_number}</span>
                        </div>

                        {row.website !== '' && (
                            <div className={`${divStyle}`}>
                                <hr />
                                <span className={`${titleStyle}`}>Website:</span>
                                <a href={`${row.website}`} target="_blank" className={`${contentStyle}`}>
                                    {row.website}
                                </a>
                            </div>
                        )}

                        <div className={`${divStyle}`}>
                            <hr />
                            <span className={`${titleStyle}`}>Hire Rates:</span>
                            <div className="flex w-full justify-between">
                                <span>British Pounds:</span>
                                <span className={`${contentStyle}`}>£ {row.hire_rate_gbp}</span>
                            </div>
                            <div className="flex w-full justify-between">
                                <span>Euros:</span>
                                <span className={`${contentStyle}`}>€ {row.hire_rate_eur}</span>
                            </div>
                            <div className="flex w-full justify-between">
                                <span>US Dollars:</span>
                                <span className={`${contentStyle}`}>$ {row.hire_rate_usd}</span>
                            </div>
                            <div className="flex w-full justify-between">
                                <span>Austrailian Dollars:</span>
                                <span className={`${contentStyle}`}>$ {row.hire_rate_aud}</span>
                            </div>
                            <div className="flex w-full justify-between">
                                <span>New Zealand Dollars:</span>
                                <span className={`${contentStyle}`}>$ {row.hire_rate_nzd}</span>
                            </div>
                            <div className="flex w-full justify-between">
                                <span>South African Rands:</span>
                                <span className={`${contentStyle}`}>R {row.hire_rate_zar}</span>
                            </div>
                        </div>

                        <div className={`${divStyle}`}>
                            <hr />
                            <span className={`${titleStyle}`}>Tags:</span>
                            {row.tags.map((itm: string, index: number) => (
                                <span key={index} className={`${contentStyle}`}>
                                    {itm}
                                </span>
                            ))}
                        </div>

                        {row.company_logo && row.company_logo[0] !== null && (
                            <div className={`${divStyle}`}>
                                <hr />
                                <span className={`${titleStyle}`}>Company Logo:</span>
                                <div className="flex flex-wrap items-center justify-center gap-2">
                                    {row.company_logo.map((itm: string, index: number) => (
                                        <img key={index} src={itm} className="h-20 rounded-md shadow-md" alt={`company-logo-${index + 1}`} />
                                    ))}
                                </div>
                            </div>
                        )}

                        {row.photo_gallery && row.photo_gallery[0] !== null && (
                            <div className={`${divStyle}`}>
                                <hr />
                                <span className={`${titleStyle}`}>Photo Gallery:</span>
                                <div className="flex flex-wrap items-center justify-center gap-2">
                                    {row.photo_gallery.map((itm: string, index: number) => (
                                        <img key={index} src={itm} className="h-20 rounded-md shadow-md" alt={`gallery-image-${index + 1}`} />
                                    ))}
                                </div>
                            </div>
                        )}

                        {row.attachments && row.attachments[0] !== null && (
                            <div className={`${divStyle}`}>
                                <hr />
                                <span className={`${titleStyle}`}>Attachments:</span>
                                {row.attachments.map((itm: string, index: number) => (
                                    <span key={index} className={`${contentStyle}`}>
                                        {itm}
                                    </span>
                                ))}
                            </div>
                        )}

                        {row.social_networks && row.social_networks[0] !== null && (
                            <div className={`${divStyle}`}>
                                <hr />
                                <span className={`${titleStyle}`}>Social Networks:</span>
                                {row.social_networks.map((itm: string, index: number) => (
                                    <span key={index} className={`${contentStyle}`}>
                                        {itm}
                                    </span>
                                ))}
                            </div>
                        )}

                        <div className={`${divStyle}`}>
                            <hr />
                            <span className={`${titleStyle}`}>Location:</span>
                            <span className={`${contentStyle}`}>{row.location}</span>
                        </div>
                        <div className="flex flex-row gap-5">
                            <hr />
                            <div className={`${divStyle}`}>
                                <span className={`${titleStyle}`}>Latitude:</span>
                                <span className={`${contentStyle}`}>{row.latitude}</span>
                            </div>
                            <div className={`${divStyle}`}>
                                <span className={`${titleStyle}`}>Longitude:</span>
                                <span className={`${contentStyle}`}>{row.longitude}</span>
                            </div>
                        </div>
                        <div className={`${divStyle}`}>
                            <hr />
                            <span className={`${titleStyle}`}>Region:</span>
                            <span className={`${contentStyle}`}>{row.region}</span>
                        </div>
                        {row.related_listing && row.related_listing.length > 0 && (
                            <div className={`${divStyle}`}>
                                <hr />
                                <span className={`${titleStyle}`}>Related Listing:</span>
                                {row.related_listing.map((itm: string, index: number) => (
                                    <a key={index} href={`${itm}`} target="_blank" className="truncate" />
                                ))}
                            </div>
                        )}

                        {row.hire_rental !== '' && (
                            <div className={`${divStyle}`}>
                                <hr />
                                <span className={`${titleStyle}`}>Hire Rental:</span>
                                <span className={`${contentStyle}`}>{row.hire_rental}</span>
                            </div>
                        )}

                        {/* ADDITIONAL */}
                        {row.additional_1 && (
                            <div className={`${divStyle}`}>
                                <hr />
                                <span className={`${titleStyle}`}>Additional 1:</span>
                                <span className={`${contentStyle}`}>{row.additional_1}</span>
                            </div>
                        )}
                        {row.additional_2 && (
                            <div className={`${divStyle}`}>
                                <hr />
                                <span className={`${titleStyle}`}>Additional 2:</span>
                                <span className={`${contentStyle}`}>{row.additional_2}</span>
                            </div>
                        )}
                        {row.additional_3 && (
                            <div className={`${divStyle}`}>
                                <hr />
                                <span className={`${titleStyle}`}>Additional 3:</span>
                                <span className={`${contentStyle}`}>{row.additional_3}</span>
                            </div>
                        )}
                        {row.additional_4 && (
                            <div className={`${divStyle}`}>
                                <hr />
                                <span className={`${titleStyle}`}>Additional 4:</span>
                                <span className={`${contentStyle}`}>{row.additional_4}</span>
                            </div>
                        )}
                        {row.additional_5 && (
                            <div className={`${divStyle}`}>
                                <hr />
                                <span className={`${titleStyle}`}>Additional 5:</span>
                                <span className={`${contentStyle}`}>{row.additional_5}</span>
                            </div>
                        )}
                        {row.additional_6 && (
                            <div className={`${divStyle}`}>
                                <hr />
                                <span className={`${titleStyle}`}>Additional 6:</span>
                                <span className={`${contentStyle}`}>{row.additional_6}</span>
                            </div>
                        )}
                        {row.additional_7 && (
                            <div className={`${divStyle}`}>
                                <hr />
                                <span className={`${titleStyle}`}>Additional 7:</span>
                                <span className={`${contentStyle}`}>{row.additional_7}</span>
                            </div>
                        )}
                        {row.additional_8 && (
                            <div className={`${divStyle}`}>
                                <hr />
                                <span className={`${titleStyle}`}>Additional 8:</span>
                                <span className={`${contentStyle}`}>{row.additional_8}</span>
                            </div>
                        )}
                        {row.additional_9 && (
                            <div className={`${divStyle}`}>
                                <hr />
                                <span className={`${titleStyle}`}>Additional 9:</span>
                                <span className={`${contentStyle}`}>{row.additional_9}</span>
                            </div>
                        )}
                        {row.additional_10 && (
                            <div className={`${divStyle}`}>
                                <hr />
                                <span className={`${titleStyle}`}>Additional 10:</span>
                                <span className={`${contentStyle}`}>{row.additional_10}</span>
                            </div>
                        )}
                    </ScrollArea>
                </AlertDialogHeader>
                <AlertDialogFooter>
                    <AlertDialogCancel>Close</AlertDialogCancel>
                </AlertDialogFooter>
            </AlertDialogContent>
        </AlertDialog>
    );
}
