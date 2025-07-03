import Machinezo from '@/assets/Machinezo.webp';
import HeroBackground from '@/components/hero-background';
import HeroImage from '@/components/hero-image';
import { type SharedData } from '@/types';
import { Head, Link, usePage } from '@inertiajs/react';
import { useEffect, useState } from 'react';

export default function Welcome() {
    const { auth } = usePage<SharedData>().props;
    const [isSafari, setIsSafari] = useState<boolean>(false);
    const safari = navigator.userAgent.includes('Safari');
    const chrome = navigator.userAgent.includes('Chrome');

    useEffect(() => {
        if (safari && !chrome) {
            setIsSafari(true);
        }
    }, [safari, chrome]);

    return (
        <>
            <Head title="Welcome">
                <link rel="preconnect" href="https://fonts.bunny.net" />
                <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600" rel="stylesheet" />
            </Head>
            <div className="relative flex min-h-screen flex-col items-center justify-center overflow-hidden bg-white p-6 text-zinc-800 lg:p-8">
                <div className="absolute top-0 left-0 z-20 flex h-full w-full bg-gradient-to-b from-white to-white/0" />
                <HeroBackground />
                <header className="absolute top-0 left-0 z-30 w-full p-5">
                    <nav className="flex items-center justify-between gap-4">
                        <div className="flex w-28 items-center justify-center rounded-md bg-blue-600 p-2 shadow-lg">
                            <img src={Machinezo} alt="machinezo" />
                        </div>
                        <div>
                            {auth.user ? (
                                <Link
                                    href={route('upload-csv')}
                                    className="inline-block rounded-sm border border-zinc-800 px-5 py-1.5 text-sm leading-normal text-zinc-800 hover:border-zinc-500"
                                >
                                    Start Uploading
                                </Link>
                            ) : (
                                <>
                                    <Link
                                        href={route('login')}
                                        className="inline-block rounded-sm border border-transparent px-5 py-1.5 text-sm leading-normal text-zinc-800 hover:border-zinc-500"
                                    >
                                        Log in
                                    </Link>
                                    <Link
                                        href={route('register')}
                                        className="inline-block rounded-sm border border-[#19140035] px-5 py-1.5 text-sm leading-normal text-[#1b1b18] hover:border-[#1915014a]"
                                    >
                                        Register
                                    </Link>
                                </>
                            )}
                        </div>
                    </nav>
                </header>
                <main className="mt-12 flex w-full flex-col gap-8">
                    <div className="z-30 flex flex-col items-center justify-center gap-10 text-center">
                        <h1 className="text-4xl leading-tight font-bold sm:text-6xl">
                            <span className="bg-gradient-to-r from-[#3546ff] via-[#1e87f7] to-[#00d0ff] bg-clip-text text-transparent">
                                Machinezo
                            </span>
                            <br />
                            Listing Creation Assistant
                        </h1>
                        <p className="mb-2 text-2xl text-zinc-500">
                            Create multiple Machinzo listings from a single csv file
                            <br />
                            and generate content using AI.
                        </p>
                    </div>
                    <div className="z-30 flex min-h-80 w-full items-center justify-center">
                        <HeroImage />
                    </div>
                </main>
            </div>
        </>
    );
}
