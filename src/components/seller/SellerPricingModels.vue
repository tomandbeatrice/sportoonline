<template>
  <div class="seller-pricing-models">
    <div class="header">
      <h1>🎯 Satıcı Olarak Başlayın</h1>
      <p class="subtitle">Size en uygun modeli seçin ve hemen satışa başlayın</p>
    </div>

    <!-- Model Comparison -->
    <div class="model-comparison">
      <div class="comparison-card commission-only">
        <div class="badge">En Kolay Başlangıç</div>
        <h2>💰 Sadece Komisyon</h2>
        <p class="description">Hiç ödeme yapmadan başlayın, sadece satış yaptığınızda komisyon ödeyin</p>
        
        <div class="pricing">
          <div class="price-row">
            <span class="label">Aylık Ücret:</span>
            <span class="value free">₺0</span>
          </div>
          <div class="price-row highlight">
            <span class="label">Satış Komisyonu:</span>
            <span class="value">%20</span>
          </div>
          <div class="price-row">
            <span class="label">Ürün Limiti:</span>
            <span class="value">Sınırsız</span>
          </div>
        </div>

        <div class="features">
          <h4>Özellikler:</h4>
          <ul>
            <li>✅ Sınırsız ürün ekleyin</li>
            <li>✅ Ürün başına 10 fotoğraf</li>
            <li>✅ Temel raporlama</li>
            <li>✅ Email destek</li>
            <li>✅ Hemen başlayın</li>
          </ul>
        </div>

        <div class="example">
          <strong>Örnek:</strong> ₺10,000 satış yaparsanız
          <div class="calculation">
            <span>Satış: ₺10,000</span>
            <span class="deduct">- Komisyon (%20): ₺2,000</span>
            <span class="result">= Kazancınız: ₺8,000</span>
          </div>
        </div>

        <button class="select-btn commission" @click="selectPlan('commission-only')">
          Bu Modeli Seç
        </button>
      </div>

      <div class="comparison-card subscription-model">
        <div class="badge premium">En Karlı</div>
        <h2>📦 Abonelik + İndirimli Komisyon</h2>
        <p class="description">Aylık ücret ödeyerek çok daha düşük komisyon ödeyin</p>

        <div class="plan-selector">
          <div 
            v-for="plan in subscriptionPlans" 
            :key="plan.slug"
            class="plan-option"
            :class="{ selected: selectedPlan?.slug === plan.slug }"
            @click="selectedPlan = plan"
          >
            <div class="plan-header">
              <h3>{{ plan.name }}</h3>
              <span class="product-limit">{{ formatProductLimit(plan.product_limit) }} ürün</span>
            </div>
            <div class="plan-pricing">
              <span class="monthly-fee">₺{{ plan.price }}/ay</span>
              <span class="commission">+ %{{ plan.commission_rate }} komisyon</span>
            </div>
          </div>
        </div>

        <div v-if="selectedPlan" class="selected-plan-details">
          <h4>{{ selectedPlan.name }} - Özellikler:</h4>
          <ul class="features-list">
            <li v-for="feature in selectedPlan.features" :key="feature">{{ feature }}</li>
          </ul>

          <div class="example">
            <strong>Örnek:</strong> ₺10,000 satış yaparsanız
            <div class="calculation">
              <span>Satış: ₺10,000</span>
              <span class="deduct">- Komisyon (%{{ selectedPlan.commission_rate }}): ₺{{ (10000 * selectedPlan.commission_rate / 100).toFixed(0) }}</span>
              <span class="deduct">- Aylık Ücret: ₺{{ selectedPlan.price }}</span>
              <span class="result">= Kazancınız: ₺{{ (10000 - (10000 * selectedPlan.commission_rate / 100) - selectedPlan.price).toFixed(0) }}</span>
            </div>
            <div class="savings">
              💡 Sadece Komisyon'a göre <strong>₺{{ calculateSavings(10000, selectedPlan) }}</strong> daha fazla kazanırsınız!
            </div>
          </div>
        </div>

        <button 
          class="select-btn subscription" 
          :disabled="!selectedPlan"
          @click="selectPlan(selectedPlan?.slug)"
        >
          {{ selectedPlan ? selectedPlan.name + ' Seç' : 'Bir Plan Seçin' }}
        </button>
      </div>
    </div>

    <!-- Calculator -->
    <div class="profit-calculator">
      <h3>🧮 Kâr Hesaplayıcı</h3>
      <p>Aylık tahmini satışınızı girin, hangi modelin daha karlı olduğunu görün</p>
      
      <div class="calculator-input">
        <label>Tahmini Aylık Satış:</label>
        <input 
          type="range" 
          v-model.number="estimatedSales" 
          min="1000" 
          max="100000" 
          step="1000"
        >
        <span class="value">₺{{ formatNumber(estimatedSales) }}</span>
      </div>

      <div class="calculator-results">
        <div class="result-card">
          <h4>💰 Sadece Komisyon</h4>
          <div class="result-breakdown">
            <span>Komisyon (%20): -₺{{ formatNumber(estimatedSales * 0.20) }}</span>
            <span class="profit">Kazancınız: ₺{{ formatNumber(estimatedSales * 0.80) }}</span>
          </div>
        </div>

        <div v-if="selectedPlan" class="result-card best">
          <h4>📦 {{ selectedPlan.name }}</h4>
          <div class="result-breakdown">
            <span>Komisyon (%{{ selectedPlan.commission_rate }}): -₺{{ formatNumber(estimatedSales * selectedPlan.commission_rate / 100) }}</span>
            <span>Aylık Ücret: -₺{{ selectedPlan.price }}</span>
            <span class="profit">Kazancınız: ₺{{ formatNumber(estimatedSales - (estimatedSales * selectedPlan.commission_rate / 100) - selectedPlan.price) }}</span>
          </div>
          <div class="advantage">
            ✨ {{ formatNumber(calculateSavings(estimatedSales, selectedPlan)) }} daha fazla kazanç!
          </div>
        </div>
      </div>
    </div>

    <!-- Comparison Table -->
    <div class="full-comparison-table">
      <h3>📊 Detaylı Karşılaştırma</h3>
      <table>
        <thead>
          <tr>
            <th>Özellik</th>
            <th>Sadece Komisyon</th>
            <th>Küçük Paket</th>
            <th>Orta Paket</th>
            <th>Büyük Paket</th>
            <th>Kurumsal Paket</th>
          </tr>
        </thead>
        <tbody>
          <tr>
            <td>Aylık Ücret</td>
            <td class="highlight">₺0</td>
            <td>₺49</td>
            <td>₺149</td>
            <td>₺399</td>
            <td>₺999</td>
          </tr>
          <tr>
            <td>Komisyon Oranı</td>
            <td>%20</td>
            <td class="highlight">%12</td>
            <td class="highlight">%10</td>
            <td class="highlight">%8</td>
            <td class="highlight">%5</td>
          </tr>
          <tr>
            <td>Ürün Limiti</td>
            <td>Sınırsız</td>
            <td>50</td>
            <td>200</td>
            <td>1,000</td>
            <td>Sınırsız</td>
          </tr>
          <tr>
            <td>Toplu Yükleme</td>
            <td>❌</td>
            <td>✅</td>
            <td>✅</td>
            <td>✅</td>
            <td>✅</td>
          </tr>
          <tr>
            <td>Gelişmiş Analitik</td>
            <td>❌</td>
            <td>❌</td>
            <td>✅</td>
            <td>✅</td>
            <td>✅</td>
          </tr>
          <tr>
            <td>API Erişimi</td>
            <td>❌</td>
            <td>❌</td>
            <td>❌</td>
            <td>✅</td>
            <td>✅</td>
          </tr>
          <tr>
            <td>Destek</td>
            <td>Email</td>
            <td>Email</td>
            <td>Email + Telefon</td>
            <td>Öncelikli</td>
            <td>7/24 VIP</td>
          </tr>
        </tbody>
      </table>
    </div>
  </div>
</template>

<script>
import { ref, computed, onMounted } from 'vue';
import axios from 'axios';

export default {
  name: 'SellerPricingModels',
  setup() {
    const selectedModel = ref('commission');
    const selectedPlan = ref(null);
    const subscriptionPlans = ref([]);
    const estimatedSales = ref(10000);

    const loadPlans = async () => {
      try {
        const response = await axios.get('/api/subscriptions/plans');
        // Filter only subscription plans (exclude commission-only)
        subscriptionPlans.value = response.data.filter(plan => plan.slug !== 'commission-only');
        
        // Pre-select middle plan
        if (subscriptionPlans.value.length > 0) {
          selectedPlan.value = subscriptionPlans.value[1] || subscriptionPlans.value[0];
        }
      } catch (error) {
        console.error('Error loading plans:', error);
      }
    };

    const showBillingToggle = computed(() => {
      return selectedModel.value === 'subscription';
    });

    const formatNumber = (value) => {
      return Number(value).toLocaleString('tr-TR', { minimumFractionDigits: 0, maximumFractionDigits: 0 });
    };

    const formatProductLimit = (limit) => {
      if (limit >= 999999) return 'Sınırsız';
      if (limit >= 1000) return (limit / 1000) + 'K';
      return limit.toString();
    };

    const calculateSavings = (sales, plan) => {
      const commissionOnlyProfit = sales * 0.80; // 20% commission
      const subscriptionProfit = sales - (sales * plan.commission_rate / 100) - plan.price;
      return (subscriptionProfit - commissionOnlyProfit).toFixed(0);
    };

    const selectPlan = async (planSlug) => {
      try {
        // Here you would redirect to subscription page or open modal
        console.log('Selected plan:', planSlug);
        // Example: router.push({ name: 'SubscriptionCheckout', params: { planSlug } });
      } catch (error) {
        console.error('Error selecting plan:', error);
      }
    };

    onMounted(() => {
      loadPlans();
    });

    return {
      selectedModel,
      selectedPlan,
      subscriptionPlans,
      estimatedSales,
      showBillingToggle,
      formatNumber,
      formatProductLimit,
      calculateSavings,
      selectPlan,
    };
  },
};
</script>

<style scoped>
.seller-pricing-models {
  max-width: 1400px;
  margin: 0 auto;
  padding: 40px 20px;
}

.header {
  text-align: center;
  margin-bottom: 50px;
}

.header h1 {
  font-size: 36px;
  color: #2c3e50;
  margin-bottom: 10px;
}

.subtitle {
  font-size: 18px;
  color: #7f8c8d;
}

.model-comparison {
  display: grid;
  grid-template-columns: repeat(2, 1fr);
  gap: 30px;
  margin-bottom: 50px;
}

.comparison-card {
  background: white;
  border-radius: 12px;
  padding: 30px;
  box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
  position: relative;
}

.commission-only {
  border: 3px solid #3498db;
}

.subscription-model {
  border: 3px solid #e74c3c;
}

.badge {
  position: absolute;
  top: -15px;
  left: 20px;
  background: #3498db;
  color: white;
  padding: 5px 15px;
  border-radius: 20px;
  font-size: 12px;
  font-weight: bold;
}

.badge.premium {
  background: #e74c3c;
}

.comparison-card h2 {
  margin: 10px 0 10px;
  font-size: 24px;
}

.description {
  color: #7f8c8d;
  margin-bottom: 20px;
}

.pricing {
  background: #f8f9fa;
  padding: 20px;
  border-radius: 8px;
  margin-bottom: 20px;
}

.price-row {
  display: flex;
  justify-content: space-between;
  margin-bottom: 10px;
}

.price-row.highlight {
  background: #fff3cd;
  padding: 10px;
  border-radius: 4px;
  margin: 10px -10px;
}

.value.free {
  color: #27ae60;
  font-weight: bold;
  font-size: 24px;
}

.features h4 {
  margin-bottom: 10px;
}

.features ul {
  list-style: none;
  padding: 0;
}

.features li {
  padding: 5px 0;
}

.example {
  background: #e8f5e9;
  padding: 15px;
  border-radius: 8px;
  margin: 20px 0;
}

.calculation {
  display: flex;
  flex-direction: column;
  gap: 5px;
  margin-top: 10px;
  font-size: 14px;
}

.deduct {
  color: #e74c3c;
}

.result {
  font-weight: bold;
  font-size: 18px;
  color: #27ae60;
  border-top: 2px solid #27ae60;
  padding-top: 5px;
  margin-top: 5px;
}

.select-btn {
  width: 100%;
  padding: 15px;
  border: none;
  border-radius: 8px;
  font-size: 18px;
  font-weight: bold;
  cursor: pointer;
  transition: all 0.3s;
}

.select-btn.commission {
  background: #3498db;
  color: white;
}

.select-btn.subscription {
  background: #e74c3c;
  color: white;
}

.select-btn:hover {
  transform: translateY(-2px);
  box-shadow: 0 6px 20px rgba(0, 0, 0, 0.2);
}

.select-btn:disabled {
  background: #95a5a6;
  cursor: not-allowed;
  transform: none;
}

.plan-selector {
  display: grid;
  gap: 10px;
  margin: 20px 0;
}

.plan-option {
  padding: 15px;
  border: 2px solid #ecf0f1;
  border-radius: 8px;
  cursor: pointer;
  transition: all 0.3s;
}

.plan-option:hover {
  border-color: #3498db;
  background: #f8f9fa;
}

.plan-option.selected {
  border-color: #e74c3c;
  background: #fff5f5;
}

.plan-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 5px;
}

.plan-header h3 {
  margin: 0;
  font-size: 16px;
}

.product-limit {
  font-size: 12px;
  color: #7f8c8d;
}

.plan-pricing {
  display: flex;
  gap: 10px;
  align-items: center;
}

.monthly-fee {
  font-weight: bold;
  color: #2c3e50;
}

.commission {
  font-size: 14px;
  color: #7f8c8d;
}

.selected-plan-details {
  margin: 20px 0;
}

.features-list {
  list-style: none;
  padding: 0;
  display: grid;
  grid-template-columns: repeat(2, 1fr);
  gap: 5px;
  font-size: 14px;
}

.savings {
  background: #fff3cd;
  padding: 10px;
  border-radius: 4px;
  margin-top: 10px;
  text-align: center;
}

.profit-calculator {
  background: white;
  border-radius: 12px;
  padding: 30px;
  margin-bottom: 50px;
  box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
}

.profit-calculator h3 {
  margin-bottom: 10px;
}

.calculator-input {
  display: flex;
  align-items: center;
  gap: 20px;
  margin: 30px 0;
}

.calculator-input input[type="range"] {
  flex: 1;
}

.calculator-input .value {
  font-size: 24px;
  font-weight: bold;
  color: #2c3e50;
  min-width: 150px;
}

.calculator-results {
  display: grid;
  grid-template-columns: repeat(2, 1fr);
  gap: 20px;
}

.result-card {
  background: #f8f9fa;
  padding: 20px;
  border-radius: 8px;
  border: 2px solid #ecf0f1;
}

.result-card.best {
  border-color: #27ae60;
  background: #e8f5e9;
}

.result-breakdown {
  display: flex;
  flex-direction: column;
  gap: 5px;
  margin-top: 10px;
}

.profit {
  font-weight: bold;
  font-size: 20px;
  color: #27ae60;
  border-top: 2px solid #27ae60;
  padding-top: 10px;
  margin-top: 10px;
}

.advantage {
  background: #27ae60;
  color: white;
  padding: 10px;
  border-radius: 4px;
  text-align: center;
  margin-top: 10px;
  font-weight: bold;
}

.full-comparison-table {
  background: white;
  border-radius: 12px;
  padding: 30px;
  box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
}

table {
  width: 100%;
  border-collapse: collapse;
  margin-top: 20px;
}

th, td {
  padding: 15px;
  text-align: left;
  border-bottom: 1px solid #ecf0f1;
}

th {
  background: #f8f9fa;
  font-weight: bold;
}

td.highlight {
  background: #fff3cd;
  font-weight: bold;
}

@media (max-width: 768px) {
  .model-comparison {
    grid-template-columns: 1fr;
  }
  
  .calculator-results {
    grid-template-columns: 1fr;
  }
  
  .features-list {
    grid-template-columns: 1fr;
  }
}
</style>
