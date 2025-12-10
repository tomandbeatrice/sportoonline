import { defineStore } from 'pinia'
import { ref } from 'vue'

export interface SearchSuggestion {
  text: string;
  type: 'correction' | 'prediction' | 'related';
  score: number;
}

export const useSmartSearchStore = defineStore('smartSearch', () => {
  // Mock dictionary for "AI" logic
  const dictionary = [
    'ayakkabı', 'spor ayakkabı', 'koşu ayakkabısı', 'futbol topu', 
    'basketbol', 'forma', 'şort', 'tayt', 'mat', 'yoga matı',
    'protein tozu', 'vitamin', 'kamp çadırı', 'uyku tulumu',
    'otel', 'tatil', 'rezervasyon', 'transfer', 'vip transfer'
  ];

  const synonyms: Record<string, string[]> = {
    'pabuç': ['ayakkabı'],
    'bot': ['ayakkabı', 'krampon'],
    'top': ['futbol topu', 'basketbol topu'],
    'otel': ['konaklama', 'tatil'],
    'araba': ['transfer', 'araç']
  };

  const suggestions = ref<SearchSuggestion[]>([]);

  // Levenshtein distance for fuzzy matching
  function levenshtein(a: string, b: string): number {
    const matrix = [];
    for (let i = 0; i <= b.length; i++) matrix[i] = [i];
    for (let j = 0; j <= a.length; j++) matrix[0][j] = j;

    for (let i = 1; i <= b.length; i++) {
      for (let j = 1; j <= a.length; j++) {
        if (b.charAt(i - 1) == a.charAt(j - 1)) {
          matrix[i][j] = matrix[i - 1][j - 1];
        } else {
          matrix[i][j] = Math.min(
            matrix[i - 1][j - 1] + 1,
            Math.min(matrix[i][j - 1] + 1, matrix[i - 1][j] + 1)
          );
        }
      }
    }
    return matrix[b.length][a.length];
  }

  function analyzeQuery(query: string) {
    suggestions.value = [];
    if (!query || query.length < 2) return;

    const lowerQuery = query.toLowerCase();

    // 1. Check for exact matches or startsWith (Predictions)
    const predictions = dictionary
      .filter(word => word.startsWith(lowerQuery) && word !== lowerQuery)
      .slice(0, 3)
      .map(word => ({ text: word, type: 'prediction' as const, score: 1 }));
    
    suggestions.value.push(...predictions);

    // 2. Check for typos (Corrections) if no direct predictions
    if (predictions.length === 0) {
      const corrections = dictionary
        .map(word => ({ word, dist: levenshtein(lowerQuery, word) }))
        .filter(item => item.dist > 0 && item.dist <= 2) // Allow max 2 typos
        .sort((a, b) => a.dist - b.dist)
        .slice(0, 1)
        .map(item => ({ text: item.word, type: 'correction' as const, score: 0.8 }));
      
      suggestions.value.push(...corrections);
    }

    // 3. Check synonyms (Related)
    Object.entries(synonyms).forEach(([key, values]) => {
      if (lowerQuery.includes(key)) {
        values.forEach(val => {
          if (!suggestions.value.find(s => s.text === val)) {
            suggestions.value.push({ text: val, type: 'related' as const, score: 0.5 });
          }
        });
      }
    });
  }

  return {
    suggestions,
    analyzeQuery
  };
});
