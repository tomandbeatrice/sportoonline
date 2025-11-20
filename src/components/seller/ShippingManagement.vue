<template>
  <div class="shipping-management">
    <div class="header">
      <h2>📦 Kargo Ücretleri Yönetimi</h2>
      <p class="subtitle">Bölgelere göre kargo ücretlerinizi belirleyin</p>
    </div>

    <!-- Quick Setup -->
    <div class="quick-setup">
      <h3>⚡ Hızlı Kurulum</h3>
      <p>Tüm bölgeler için aynı kargo ücretini ayarlayın</p>
      <div class="quick-form">
        <div class="form-group">
          <label>Kargo Ücreti:</label>
          <input 
            type="number" 
            v-model.number="quickSetup.defaultFee" 
            placeholder="₺30" 
            min="0"
            step="0.01"
          >
        </div>
        <div class="form-group">
          <label>Ücretsiz Kargo Limiti (Opsiyonel):</label>
          <input 
            type="number" 
            v-model.number="quickSetup.freeShippingThreshold" 
            placeholder="₺500" 
            min="0"
            step="0.01"
          >
          <small>Bu tutarın üzerindeki siparişlerde ücretsiz kargo</small>
        </div>
        <button @click="setDefaults" class="btn-primary">
          Tüm Bölgelere Uygula
        </button>
      </div>
    </div>

    <!-- Regional Shipping Fees -->
    <div class="regional-fees">
      <h3>🗺️ Bölgesel Kargo Ücretleri</h3>
      <div class="fees-grid">
        <div 
          v-for="(regionName, regionKey) in availableRegions" 
          :key="regionKey"
          class="region-card"
        >
          <div class="region-header">
            <h4>{{ regionName }}</h4>
            <label class="switch">
              <input 
                type="checkbox" 
                :checked="isRegionActive(regionKey)"
                @change="toggleRegion(regionKey)"
              >
              <span class="slider"></span>
            </label>
          </div>
          
          <div class="region-form">
            <div class="form-group">
              <label>Kargo Ücreti:</label>
              <div class="input-group">
                <span class="prefix">₺</span>
                <input 
                  type="number" 
                  v-model.number="getRegionFee(regionKey).fee" 
                  placeholder="30.00"
                  min="0"
                  step="0.01"
                >
              </div>
            </div>
            
            <div class="form-group">
              <label>Ücretsiz Kargo Limiti:</label>
              <div class="input-group">
                <span class="prefix">₺</span>
                <input 
                  type="number" 
                  v-model.number="getRegionFee(regionKey).threshold" 
                  placeholder="0"
                  min="0"
                  step="0.01"
                >
              </div>
              <small v-if="getRegionFee(regionKey).threshold > 0">
                ₺{{ getRegionFee(regionKey).threshold }} ve üzeri ücretsiz
              </small>
            </div>
            
            <button 
              @click="saveRegion(regionKey)" 
              class="btn-save"
            >
              Kaydet
            </button>
          </div>
        </div>
      </div>
    </div>

    <!-- Calculator -->
    <div class="shipping-calculator">
      <h3>🧮 Kargo Hesaplayıcı</h3>
      <p>Belirli bir sipariş için kargo ücretini hesaplayın</p>
      
      <div class="calculator-form">
        <div class="form-group">
          <label>Şehir:</label>
          <select v-model="calculator.city">
            <option value="">Şehir Seçin</option>
            <option value="İstanbul">İstanbul</option>
            <option value="Ankara">Ankara</option>
            <option value="İzmir">İzmir</option>
            <option value="Bursa">Bursa</option>
            <option value="Antalya">Antalya</option>
            <option value="Adana">Adana</option>
            <option value="Gaziantep">Gaziantep</option>
            <option value="Konya">Konya</option>
            <option value="Trabzon">Trabzon</option>
            <option value="Diyarbakır">Diyarbakır</option>
          </select>
        </div>
        
        <div class="form-group">
          <label>Sipariş Tutarı:</label>
          <div class="input-group">
            <span class="prefix">₺</span>
            <input 
              type="number" 
              v-model.number="calculator.subtotal" 
              placeholder="1000"
              min="0"
              step="0.01"
            >
          </div>
        </div>
        
        <button @click="calculateShipping" class="btn-calculate">
          Hesapla
        </button>
      </div>
      
      <div v-if="calculatorResult" class="calculator-result">
        <div class="result-card" :class="{ 'free-shipping': calculatorResult.free_shipping }">
          <div class="result-row">
            <span>Bölge:</span>
            <span class="value">{{ availableRegions[calculatorResult.region] }}</span>
          </div>
          <div class="result-row">
            <span>Kargo Ücreti:</span>
            <span class="value" :class="{ 'free': calculatorResult.free_shipping }">
              <span v-if="calculatorResult.free_shipping" class="strikethrough">₺{{ calculatorResult.original_fee }}</span>
              ₺{{ calculatorResult.fee }}
              <span v-if="calculatorResult.free_shipping" class="badge-free">ÜCRETSIZ</span>
            </span>
          </div>
          <div v-if="calculatorResult.threshold > 0" class="result-row">
            <span>Ücretsiz Kargo Limiti:</span>
            <span class="value">₺{{ calculatorResult.threshold }}</span>
          </div>
        </div>
      </div>
    </div>

    <!-- Info Box -->
    <div class="info-box">
      <h4>ℹ️ Önemli Bilgiler</h4>
      <ul>
        <li>Kargo ücreti <strong>satıcı tarafından karşılanır</strong> (müşteri ödese bile sizden kesilir)</li>
        <li>Müşteri kargo ücretini sipariş toplamına eklenir</li>
        <li>Aylık komisyon hesabınızdan kargo ücretleri düşülür</li>
        <li>Ücretsiz kargo kampanyası yaparak satışlarınızı artırabilirsiniz</li>
      </ul>
    </div>
  </div>
</template>

<script>
import { ref, reactive, onMounted } from 'vue';
import axios from 'axios';

export default {
  name: 'ShippingManagement',
  setup() {
    const shippingFees = ref([]);
    const availableRegions = ref({});
    
    const quickSetup = reactive({
      defaultFee: 30,
      freeShippingThreshold: 0,
    });

    const regionFees = reactive({});

    const calculator = reactive({
      city: '',
      subtotal: 1000,
    });

    const calculatorResult = ref(null);

    const loadShippingFees = async () => {
      try {
        const response = await axios.get('/api/seller/shipping');
        shippingFees.value = response.data.shipping_fees;
        availableRegions.value = response.data.available_regions;

        // Initialize region fees
        Object.keys(availableRegions.value).forEach(region => {
          const existing = shippingFees.value.find(f => f.region === region);
          regionFees[region] = {
            fee: existing?.fee || 30,
            threshold: existing?.free_shipping_threshold || 0,
            is_active: existing?.is_active ?? true,
          };
        });
      } catch (error) {
        console.error('Error loading shipping fees:', error);
      }
    };

    const setDefaults = async () => {
      try {
        await axios.post('/api/seller/shipping/set-defaults', {
          default_fee: quickSetup.defaultFee,
          free_shipping_threshold: quickSetup.freeShippingThreshold,
        });
        
        alert('Varsayılan kargo ücretleri tüm bölgelere uygulandı!');
        loadShippingFees();
      } catch (error) {
        console.error('Error setting defaults:', error);
        alert('Hata oluştu');
      }
    };

    const getRegionFee = (region) => {
      if (!regionFees[region]) {
        regionFees[region] = {
          fee: 30,
          threshold: 0,
          is_active: true,
        };
      }
      return regionFees[region];
    };

    const isRegionActive = (region) => {
      return regionFees[region]?.is_active ?? true;
    };

    const toggleRegion = async (region) => {
      const existing = shippingFees.value.find(f => f.region === region);
      if (existing) {
        try {
          await axios.post(`/api/seller/shipping/${existing.id}/toggle`);
          loadShippingFees();
        } catch (error) {
          console.error('Error toggling region:', error);
        }
      }
    };

    const saveRegion = async (region) => {
      try {
        await axios.post('/api/seller/shipping/upsert', {
          region: region,
          fee: regionFees[region].fee,
          free_shipping_threshold: regionFees[region].threshold,
          is_active: regionFees[region].is_active,
        });
        
        alert(`${availableRegions.value[region]} için kargo ücreti kaydedildi!`);
        loadShippingFees();
      } catch (error) {
        console.error('Error saving region:', error);
        alert('Hata oluştu');
      }
    };

    const calculateShipping = async () => {
      if (!calculator.city || calculator.subtotal <= 0) {
        alert('Lütfen şehir ve sipariş tutarını girin');
        return;
      }

      try {
        const response = await axios.post('/api/seller/shipping/calculate', {
          city: calculator.city,
          subtotal: calculator.subtotal,
        });
        
        calculatorResult.value = {
          ...response.data,
          original_fee: response.data.free_shipping ? regionFees[response.data.region]?.fee || 30 : response.data.fee,
        };
      } catch (error) {
        console.error('Error calculating shipping:', error);
        alert('Hata oluştu');
      }
    };

    onMounted(() => {
      loadShippingFees();
    });

    return {
      shippingFees,
      availableRegions,
      quickSetup,
      regionFees,
      calculator,
      calculatorResult,
      setDefaults,
      getRegionFee,
      isRegionActive,
      toggleRegion,
      saveRegion,
      calculateShipping,
    };
  },
};
</script>

<style scoped>
.shipping-management {
  max-width: 1200px;
  margin: 0 auto;
  padding: 20px;
}

.header {
  margin-bottom: 30px;
}

.header h2 {
  margin: 0 0 10px;
  font-size: 28px;
}

.subtitle {
  color: #7f8c8d;
}

.quick-setup {
  background: white;
  border-radius: 8px;
  padding: 24px;
  margin-bottom: 30px;
  box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
}

.quick-setup h3 {
  margin: 0 0 10px;
}

.quick-form {
  display: grid;
  grid-template-columns: 1fr 1fr auto;
  gap: 20px;
  align-items: end;
  margin-top: 20px;
}

.form-group {
  display: flex;
  flex-direction: column;
  gap: 8px;
}

.form-group label {
  font-weight: 600;
  font-size: 14px;
}

.form-group input, .form-group select {
  padding: 10px;
  border: 1px solid #ddd;
  border-radius: 4px;
  font-size: 14px;
}

.form-group small {
  font-size: 12px;
  color: #7f8c8d;
}

.input-group {
  position: relative;
}

.input-group .prefix {
  position: absolute;
  left: 12px;
  top: 50%;
  transform: translateY(-50%);
  color: #7f8c8d;
  font-weight: 600;
}

.input-group input {
  padding-left: 30px;
}

.btn-primary, .btn-save, .btn-calculate {
  padding: 12px 24px;
  border: none;
  border-radius: 4px;
  font-weight: 600;
  cursor: pointer;
  transition: all 0.3s;
}

.btn-primary {
  background: #3498db;
  color: white;
}

.btn-primary:hover {
  background: #2980b9;
}

.btn-save {
  background: #27ae60;
  color: white;
  width: 100%;
}

.btn-save:hover {
  background: #229954;
}

.btn-calculate {
  background: #e74c3c;
  color: white;
}

.btn-calculate:hover {
  background: #c0392b;
}

.regional-fees {
  background: white;
  border-radius: 8px;
  padding: 24px;
  margin-bottom: 30px;
  box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
}

.fees-grid {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
  gap: 20px;
  margin-top: 20px;
}

.region-card {
  border: 2px solid #ecf0f1;
  border-radius: 8px;
  padding: 16px;
  transition: all 0.3s;
}

.region-card:hover {
  border-color: #3498db;
  box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
}

.region-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 16px;
  padding-bottom: 12px;
  border-bottom: 1px solid #ecf0f1;
}

.region-header h4 {
  margin: 0;
  font-size: 16px;
}

.switch {
  position: relative;
  display: inline-block;
  width: 50px;
  height: 24px;
}

.switch input {
  opacity: 0;
  width: 0;
  height: 0;
}

.slider {
  position: absolute;
  cursor: pointer;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background-color: #ccc;
  transition: 0.4s;
  border-radius: 24px;
}

.slider:before {
  position: absolute;
  content: "";
  height: 18px;
  width: 18px;
  left: 3px;
  bottom: 3px;
  background-color: white;
  transition: 0.4s;
  border-radius: 50%;
}

input:checked + .slider {
  background-color: #27ae60;
}

input:checked + .slider:before {
  transform: translateX(26px);
}

.region-form {
  display: flex;
  flex-direction: column;
  gap: 12px;
}

.shipping-calculator {
  background: white;
  border-radius: 8px;
  padding: 24px;
  margin-bottom: 30px;
  box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
}

.calculator-form {
  display: grid;
  grid-template-columns: 1fr 1fr auto;
  gap: 20px;
  align-items: end;
  margin-top: 20px;
}

.calculator-result {
  margin-top: 20px;
  padding-top: 20px;
  border-top: 2px solid #ecf0f1;
}

.result-card {
  background: #f8f9fa;
  padding: 20px;
  border-radius: 8px;
  border-left: 4px solid #3498db;
}

.result-card.free-shipping {
  background: #d4edda;
  border-left-color: #27ae60;
}

.result-row {
  display: flex;
  justify-content: space-between;
  padding: 8px 0;
  border-bottom: 1px solid #dee2e6;
}

.result-row:last-child {
  border-bottom: none;
}

.value {
  font-weight: 600;
}

.value.free {
  color: #27ae60;
}

.strikethrough {
  text-decoration: line-through;
  color: #95a5a6;
  margin-right: 8px;
}

.badge-free {
  background: #27ae60;
  color: white;
  padding: 2px 8px;
  border-radius: 12px;
  font-size: 11px;
  margin-left: 8px;
}

.info-box {
  background: #fff3cd;
  border: 1px solid #ffc107;
  border-radius: 8px;
  padding: 20px;
}

.info-box h4 {
  margin: 0 0 12px;
  color: #856404;
}

.info-box ul {
  margin: 0;
  padding-left: 20px;
  color: #856404;
}

.info-box li {
  margin-bottom: 8px;
}

@media (max-width: 768px) {
  .quick-form, .calculator-form {
    grid-template-columns: 1fr;
  }
  
  .fees-grid {
    grid-template-columns: 1fr;
  }
}
</style>
