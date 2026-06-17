import { createApp } from 'vue';

const mountElement = document.querySelector('[data-deployment-status-widget]');

if (mountElement) {
    createApp({
        data() {
            return {
                loading: true,
                error: null,
                total: 0,
                statuses: [],
            };
        },
        computed: {
            hasDeployments() {
                return this.total > 0;
            },
        },
        async mounted() {
            try {
                const response = await fetch(mountElement.dataset.statusUrl, {
                    headers: { Accept: 'application/json' },
                });

                if (!response.ok) {
                    throw new Error('Impossible de charger les statuts.');
                }

                const payload = await response.json();
                this.total = payload.total;
                this.statuses = payload.statuses;
            } catch (error) {
                this.error = error.message;
            } finally {
                this.loading = false;
            }
        },
        template: `
            <section class="panel status-widget" aria-live="polite">
                <div class="status-widget__header">
                    <div>
                        <h2>Statuts des demandes</h2>
                        <p class="muted" v-if="!loading && !error">{{ total }} demande{{ total > 1 ? 's' : '' }} suivie{{ total > 1 ? 's' : '' }}</p>
                    </div>
                    <span class="badge" v-if="loading">Chargement</span>
                    <span class="badge badge--failed" v-if="error">Erreur</span>
                </div>

                <p class="muted" v-if="loading">Chargement des statuts...</p>
                <p class="muted" v-else-if="error">{{ error }}</p>
                <p class="muted" v-else-if="!hasDeployments">Aucune demande a afficher.</p>

                <div v-else class="status-widget__list">
                    <div class="status-widget__row" v-for="item in statuses" :key="item.status">
                        <div class="status-widget__meta">
                            <span :class="'badge badge--' + item.status">{{ item.label }}</span>
                            <strong>{{ item.count }}</strong>
                        </div>
                        <div class="status-widget__bar" :aria-label="item.label + ' : ' + item.percentage + '%'">
                            <span :class="'status-widget__fill status-widget__fill--' + item.status" :style="{ width: item.percentage + '%' }"></span>
                        </div>
                    </div>
                </div>
            </section>
        `,
    }).mount(mountElement);
}
