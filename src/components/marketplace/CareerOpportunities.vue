<!-- 
  CareerOpportunities.vue - Career (Kariyer) Module
  Displays job openings with application functionality
-->
<template>
  <div class="career-opportunities min-h-screen bg-slate-50">
    <!-- Header Section -->
    <section class="bg-gradient-to-br from-amber-500 to-orange-500 py-12 md:py-16">
      <div class="max-w-7xl mx-auto px-4 sm:px-6">
        <div class="text-center text-white">
          <div class="inline-flex items-center gap-2 px-4 py-2 bg-white/20 backdrop-blur-sm rounded-full text-sm font-medium mb-4">
            <span class="w-2 h-2 bg-green-400 rounded-full animate-pulse"></span>
            {{ jobOpenings.length }} Açık Pozisyon
          </div>
          <h1 class="text-3xl md:text-4xl lg:text-5xl font-black mb-4">
            💼 Kariyer Fırsatları
          </h1>
          <p class="text-lg md:text-xl text-white/90 max-w-2xl mx-auto">
            Ekibimize katılın! Spor ve teknoloji dünyasında kariyerinizi şekillendirin.
          </p>
        </div>
      </div>
    </section>

    <!-- Job Listings -->
    <section class="py-12 md:py-16">
      <div class="max-w-7xl mx-auto px-4 sm:px-6">
        <!-- Filter & Sort (Future Enhancement) -->
        <div class="flex items-center justify-between mb-8">
          <h2 class="text-2xl font-bold text-slate-900">Açık Pozisyonlar</h2>
          <div class="flex items-center gap-3">
            <span class="text-sm text-slate-500">{{ jobOpenings.length }} İlan</span>
          </div>
        </div>

        <!-- Job Cards Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
          <div
            v-for="job in jobOpenings"
            :key="job.id"
            class="job-card bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden hover:shadow-lg hover:-translate-y-1 transition-all group"
          >
            <!-- Job Header -->
            <div class="p-6">
              <div class="flex items-start justify-between mb-4">
                <div class="flex-1">
                  <h3 class="text-xl font-bold text-slate-900 mb-2 group-hover:text-amber-600 transition-colors">
                    {{ job.title }}
                  </h3>
                  <div class="flex flex-wrap gap-2 mb-3">
                    <span class="inline-flex items-center gap-1 px-3 py-1 bg-amber-50 text-amber-600 rounded-full text-xs font-medium">
                      <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                      </svg>
                      {{ job.department }}
                    </span>
                    <span class="inline-flex items-center gap-1 px-3 py-1 bg-blue-50 text-blue-600 rounded-full text-xs font-medium">
                      <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                      </svg>
                      {{ job.location }}
                    </span>
                  </div>
                </div>
                <div class="ml-4">
                  <div class="w-16 h-16 bg-gradient-to-br from-amber-400 to-orange-500 rounded-xl flex items-center justify-center shadow-lg shadow-amber-200">
                    <span class="text-3xl">{{ job.icon }}</span>
                  </div>
                </div>
              </div>

              <!-- Job Description -->
              <p class="text-slate-600 text-sm mb-4 line-clamp-3">
                {{ job.description }}
              </p>

              <!-- Job Details -->
              <div class="flex flex-wrap gap-2 mb-4">
                <span
                  v-for="tag in job.tags"
                  :key="tag"
                  class="px-2 py-1 bg-slate-100 text-slate-600 rounded-md text-xs"
                >
                  {{ tag }}
                </span>
              </div>

              <!-- Apply Button -->
              <button
                @click="openApplicationForm(job)"
                class="w-full py-3 bg-amber-600 text-white font-bold rounded-xl hover:bg-amber-700 transition-colors shadow-md shadow-amber-200 group-hover:shadow-lg group-hover:shadow-amber-300"
              >
                Başvur
              </button>
            </div>
          </div>
        </div>

        <!-- Empty State -->
        <div v-if="jobOpenings.length === 0" class="text-center py-12 bg-white rounded-2xl">
          <div class="text-4xl mb-3">💼</div>
          <p class="text-slate-500">Şu anda açık pozisyon bulunmamaktadır.</p>
        </div>
      </div>
    </section>

    <!-- Application Form Modal -->
    <Transition name="modal">
      <div
        v-if="showApplicationForm"
        class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/50"
        @click.self="closeApplicationForm"
      >
        <div
          class="bg-white rounded-2xl shadow-xl max-w-2xl w-full max-h-[90vh] overflow-y-auto"
          @click.stop
        >
          <!-- Modal Header -->
          <div class="sticky top-0 bg-white border-b border-slate-200 p-6 flex items-center justify-between">
            <div>
              <h2 class="text-2xl font-bold text-slate-900">Başvuru Formu</h2>
              <p class="text-sm text-slate-500 mt-1">{{ selectedJob?.title }}</p>
            </div>
            <button
              @click="closeApplicationForm"
              class="p-2 text-slate-400 hover:text-slate-600 hover:bg-slate-100 rounded-lg transition-colors"
            >
              <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
              </svg>
            </button>
          </div>

          <!-- Modal Body -->
          <form @submit.prevent="submitApplication" class="p-6 space-y-6">
            <!-- Name Field -->
            <div>
              <label for="applicant-name" class="block text-sm font-medium text-slate-700 mb-2">
                Ad Soyad <span class="text-red-500">*</span>
              </label>
              <input
                id="applicant-name"
                v-model="applicationForm.name"
                type="text"
                required
                placeholder="Adınız ve Soyadınız"
                class="w-full px-4 py-3 border border-slate-300 rounded-xl focus:ring-2 focus:ring-amber-500 focus:border-amber-500 transition-all"
              />
            </div>

            <!-- Email Field -->
            <div>
              <label for="applicant-email" class="block text-sm font-medium text-slate-700 mb-2">
                E-posta <span class="text-red-500">*</span>
              </label>
              <input
                id="applicant-email"
                v-model="applicationForm.email"
                type="email"
                required
                placeholder="ornek@email.com"
                class="w-full px-4 py-3 border border-slate-300 rounded-xl focus:ring-2 focus:ring-amber-500 focus:border-amber-500 transition-all"
              />
            </div>

            <!-- Phone Field -->
            <div>
              <label for="applicant-phone" class="block text-sm font-medium text-slate-700 mb-2">
                Telefon
              </label>
              <input
                id="applicant-phone"
                v-model="applicationForm.phone"
                type="tel"
                placeholder="+90 555 123 45 67"
                class="w-full px-4 py-3 border border-slate-300 rounded-xl focus:ring-2 focus:ring-amber-500 focus:border-amber-500 transition-all"
              />
            </div>

            <!-- Resume Upload (Simulation) -->
            <div>
              <label for="applicant-resume" class="block text-sm font-medium text-slate-700 mb-2">
                Özgeçmiş (CV) <span class="text-red-500">*</span>
              </label>
              <div class="relative">
                <input
                  id="applicant-resume"
                  type="file"
                  accept=".pdf,.doc,.docx"
                  required
                  @change="handleResumeUpload"
                  class="hidden"
                  ref="resumeInput"
                />
                <button
                  type="button"
                  @click="triggerResumeUpload"
                  class="w-full px-4 py-3 border-2 border-dashed border-slate-300 rounded-xl hover:border-amber-500 hover:bg-amber-50 transition-all text-left"
                >
                  <div class="flex items-center gap-3">
                    <div class="w-10 h-10 bg-amber-100 rounded-lg flex items-center justify-center">
                      <svg class="w-5 h-5 text-amber-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/>
                      </svg>
                    </div>
                    <div class="flex-1">
                      <p class="text-sm font-medium text-slate-700">
                        {{ applicationForm.resumeFileName || 'CV Yükle (PDF, DOC, DOCX)' }}
                      </p>
                      <p class="text-xs text-slate-500">Maksimum 5MB</p>
                    </div>
                  </div>
                </button>
              </div>
            </div>

            <!-- Cover Letter (Optional) -->
            <div>
              <label for="applicant-message" class="block text-sm font-medium text-slate-700 mb-2">
                Ön Yazı (İsteğe Bağlı)
              </label>
              <textarea
                id="applicant-message"
                v-model="applicationForm.message"
                rows="4"
                placeholder="Neden bu pozisyon için uygun olduğunuzu kısaca açıklayın..."
                class="w-full px-4 py-3 border border-slate-300 rounded-xl focus:ring-2 focus:ring-amber-500 focus:border-amber-500 transition-all resize-none"
              ></textarea>
            </div>

            <!-- Submit Buttons -->
            <div class="flex gap-3">
              <button
                type="button"
                @click="closeApplicationForm"
                class="flex-1 px-6 py-3 border-2 border-slate-200 text-slate-700 font-semibold rounded-xl hover:bg-slate-50 transition-all"
              >
                İptal
              </button>
              <button
                type="submit"
                :disabled="isSubmitting"
                class="flex-1 px-6 py-3 bg-amber-600 text-white font-bold rounded-xl hover:bg-amber-700 disabled:opacity-50 disabled:cursor-not-allowed transition-all shadow-lg shadow-amber-200"
              >
                <span v-if="!isSubmitting">Başvuruyu Gönder</span>
                <span v-else class="inline-flex items-center gap-2">
                  <svg class="w-5 h-5 animate-spin" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" fill="none"/>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/>
                  </svg>
                  Gönderiliyor...
                </span>
              </button>
            </div>
          </form>
        </div>
      </div>
    </Transition>

    <!-- Success Toast (Simple Implementation) -->
    <Transition name="toast">
      <div
        v-if="showSuccessToast"
        class="fixed bottom-6 right-6 z-50 max-w-md bg-green-500 text-white px-6 py-4 rounded-xl shadow-2xl flex items-center gap-3"
      >
        <svg class="w-6 h-6 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
        </svg>
        <div>
          <p class="font-semibold">Başvurunuz Alındı!</p>
          <p class="text-sm text-green-100">En kısa sürede size geri dönüş yapacağız.</p>
        </div>
      </div>
    </Transition>
  </div>
</template>

<script setup lang="ts">
import { ref } from 'vue'

// Types
interface Job {
  id: number
  title: string
  department: string
  location: string
  description: string
  icon: string
  tags: string[]
}

interface ApplicationForm {
  name: string
  email: string
  phone: string
  resumeFileName: string
  message: string
}

// Mock Job Data
const jobOpenings = ref<Job[]>([
  {
    id: 1,
    title: 'Frontend Developer',
    department: 'Yazılım',
    location: 'İstanbul',
    icon: '💻',
    description: 'Modern web teknolojileri ile kullanıcı dostu arayüzler geliştirmek için ekibimize katılın. Vue.js, React ve TypeScript deneyimi arıyoruz.',
    tags: ['Vue.js', 'TypeScript', 'Tailwind CSS', 'Full-time']
  },
  {
    id: 2,
    title: 'Backend Developer',
    department: 'Yazılım',
    location: 'İstanbul / Remote',
    icon: '⚙️',
    description: 'Yüksek performanslı API\'ler ve mikroservisler geliştirmek için deneyimli backend geliştiriciler arıyoruz. Node.js veya Laravel deneyimi gereklidir.',
    tags: ['Node.js', 'Laravel', 'PostgreSQL', 'Full-time']
  },
  {
    id: 3,
    title: 'Product Manager',
    department: 'Ürün',
    location: 'İstanbul',
    icon: '📊',
    description: 'Ürün vizyonunu belirlemek ve geliştirme ekibiyle birlikte çalışarak müşteri ihtiyaçlarını karşılayan çözümler üretmek için deneyimli ürün yöneticisi arıyoruz.',
    tags: ['Agile', 'Jira', 'Analytics', 'Full-time']
  },
  {
    id: 4,
    title: 'UI/UX Designer',
    department: 'Tasarım',
    location: 'İstanbul',
    icon: '🎨',
    description: 'Kullanıcı deneyimini ön planda tutan, yaratıcı ve yenilikçi tasarımlar yapabilen UI/UX tasarımcısı arıyoruz. Figma ve Adobe XD deneyimi önemli.',
    tags: ['Figma', 'Adobe XD', 'Prototyping', 'Full-time']
  },
  {
    id: 5,
    title: 'DevOps Engineer',
    department: 'Altyapı',
    location: 'İstanbul / Remote',
    icon: '🔧',
    description: 'CI/CD süreçlerini optimize etmek ve bulut altyapısını yönetmek için DevOps mühendisi arıyoruz. AWS, Docker ve Kubernetes deneyimi gereklidir.',
    tags: ['AWS', 'Docker', 'Kubernetes', 'Full-time']
  },
  {
    id: 6,
    title: 'Marketing Specialist',
    department: 'Pazarlama',
    location: 'İstanbul',
    icon: '📢',
    description: 'Dijital pazarlama stratejileri geliştirmek ve sosyal medya yönetimi için deneyimli pazarlama uzmanı arıyoruz. SEO ve Google Analytics bilgisi önemli.',
    tags: ['SEO', 'Google Ads', 'Social Media', 'Full-time']
  },
  {
    id: 7,
    title: 'Customer Success Manager',
    department: 'Müşteri İlişkileri',
    location: 'İstanbul',
    icon: '💬',
    description: 'Müşteri memnuniyetini artırmak ve uzun vadeli ilişkiler kurmak için müşteri başarı yöneticisi arıyoruz. İletişim becerileri ve empati önemli.',
    tags: ['CRM', 'Communication', 'Problem Solving', 'Full-time']
  },
  {
    id: 8,
    title: 'Data Analyst',
    department: 'Veri',
    location: 'İstanbul / Remote',
    icon: '📈',
    description: 'Veri analitiği ve raporlama için deneyimli veri analisti arıyoruz. SQL, Python ve veri görselleştirme araçları deneyimi gereklidir.',
    tags: ['SQL', 'Python', 'Tableau', 'Full-time']
  },
  {
    id: 9,
    title: 'Mobile Developer',
    department: 'Yazılım',
    location: 'İstanbul',
    icon: '📱',
    description: 'iOS ve Android platformları için mobil uygulama geliştirmek üzere deneyimli mobil geliştiriciler arıyoruz. React Native veya Flutter deneyimi önemli.',
    tags: ['React Native', 'Flutter', 'iOS', 'Android', 'Full-time']
  },
  {
    id: 10,
    title: 'QA Engineer',
    department: 'Kalite Güvence',
    location: 'İstanbul',
    icon: '🔍',
    description: 'Yazılım kalitesini sağlamak için test otomasyonu ve manuel test süreçlerini yürütecek QA mühendisi arıyoruz. Selenium ve Jest deneyimi önemli.',
    tags: ['Selenium', 'Jest', 'Automation', 'Full-time']
  },
  {
    id: 11,
    title: 'Content Writer',
    department: 'İçerik',
    location: 'Remote',
    icon: '✍️',
    description: 'Blog yazıları, sosyal medya içerikleri ve ürün açıklamaları yazmak için yaratıcı içerik yazarı arıyoruz. SEO bilgisi önemli.',
    tags: ['SEO', 'Copywriting', 'Content Strategy', 'Part-time']
  },
  {
    id: 12,
    title: 'HR Specialist',
    department: 'İnsan Kaynakları',
    location: 'İstanbul',
    icon: '👥',
    description: 'İşe alım süreçlerini yönetmek ve çalışan deneyimini geliştirmek için İK uzmanı arıyoruz. İletişim ve organizasyon becerileri önemli.',
    tags: ['Recruitment', 'Onboarding', 'Employee Relations', 'Full-time']
  }
])

// State
const showApplicationForm = ref(false)
const selectedJob = ref<Job | null>(null)
const isSubmitting = ref(false)
const showSuccessToast = ref(false)
const resumeInput = ref<HTMLInputElement | null>(null)

const applicationForm = ref<ApplicationForm>({
  name: '',
  email: '',
  phone: '',
  resumeFileName: '',
  message: ''
})

// Methods
const openApplicationForm = (job: Job) => {
  selectedJob.value = job
  showApplicationForm.value = true
  // Reset form
  applicationForm.value = {
    name: '',
    email: '',
    phone: '',
    resumeFileName: '',
    message: ''
  }
}

const closeApplicationForm = () => {
  showApplicationForm.value = false
  selectedJob.value = null
}

const triggerResumeUpload = () => {
  resumeInput.value?.click()
}

const handleResumeUpload = (event: Event) => {
  const target = event.target as HTMLInputElement
  const file = target.files?.[0]
  if (file) {
    // Validate file size (5MB max)
    if (file.size > 5 * 1024 * 1024) {
      alert('Dosya boyutu 5MB\'dan küçük olmalıdır.')
      target.value = ''
      return
    }
    applicationForm.value.resumeFileName = file.name
  }
}

const submitApplication = async () => {
  isSubmitting.value = true

  // Simulate API call
  await new Promise(resolve => setTimeout(resolve, 1500))

  // In a real application, you would send the data to the backend here
  console.log('Application submitted:', {
    job: selectedJob.value,
    application: applicationForm.value
  })

  isSubmitting.value = false
  closeApplicationForm()

  // Show success toast
  showSuccessToast.value = true
  setTimeout(() => {
    showSuccessToast.value = false
  }, 5000)
}
</script>

<style scoped>
/* Line clamp utility */
.line-clamp-3 {
  display: -webkit-box;
  -webkit-line-clamp: 3;
  -webkit-box-orient: vertical;
  overflow: hidden;
}

/* Modal animations */
.modal-enter-active,
.modal-leave-active {
  transition: all 0.3s ease;
}

.modal-enter-from,
.modal-leave-to {
  opacity: 0;
}

.modal-enter-from > div,
.modal-leave-to > div {
  transform: scale(0.95);
}

/* Toast animations */
.toast-enter-active,
.toast-leave-active {
  transition: all 0.3s ease;
}

.toast-enter-from,
.toast-leave-to {
  opacity: 0;
  transform: translateY(20px);
}

/* Custom scrollbar for modal */
@media (min-width: 768px) {
  .overflow-y-auto::-webkit-scrollbar {
    width: 6px;
  }

  .overflow-y-auto::-webkit-scrollbar-track {
    background: #f1f5f9;
    border-radius: 10px;
  }

  .overflow-y-auto::-webkit-scrollbar-thumb {
    background: #cbd5e1;
    border-radius: 10px;
  }

  .overflow-y-auto::-webkit-scrollbar-thumb:hover {
    background: #94a3b8;
  }
}
</style>
