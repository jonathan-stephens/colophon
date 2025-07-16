let translations = $state<Record<string, string>>({});

export const t = (key: string, fallback?: string): string => {
    return translations[key] || fallback || key;
};

export const tt = (key: string, fallback: string, replacements: Record<string, string>): string => {
    let text = translations[key] || fallback || key;
    
    for (const [placeholder, value] of Object.entries(replacements)) {
        text = text.replace(`{${placeholder}}`, value);
    }
    
    return text;
};

export const setTranslations = (newTranslations: Record<string, string>) => {
    translations = newTranslations;
};