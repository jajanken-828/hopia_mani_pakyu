<script setup>
import { ref, computed, onMounted } from 'vue';
import { Head } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import {
    Search, Filter, X, ChevronRight, Package, Layers,
    Tag, Ruler, Weight, Palette, Clock, CheckCircle,
    AlertTriangle, Boxes, ShoppingCart, TrendingUp,
    ChevronDown, Info, Star, Zap, ArrowUpRight,
} from 'lucide-vue-next';

// ── Dummy Product Catalog ─────────────────────────────────────────────────────
const products = ref([
    {
        id: 'PRD-001',
        name: 'Classic Blue Crew-Neck T-Shirt',
        sku: 'TSH-BLU-CNK-001',
        category: 'Apparel',
        subcategory: 'T-Shirts',
        status: 'Active',
        colorTag: 'blue',
        colorHex: '#1d4ed8',
        colorName: 'Royal Blue',
        sizes: ['XS', 'S', 'M', 'L', 'XL', 'XXL'],
        weight: '180 gsm',
        dimensions: '70cm × 50cm (M size)',
        batchSize: 500,
        leadTime: '7 days',
        unitCost: 285.00,
        sellingPrice: 650.00,
        stockOnHand: 1240,
        moq: 100,
        certification: 'OEKO-TEX Standard 100',
        description: 'Heavyweight 100% combed cotton crew-neck tee with ribbed collar and double-needle stitching at hem and cuffs. Pre-shrunk and enzyme-washed for a soft hand feel.',
        specs: [
            { label: 'Fabric Composition', value: '100% Combed Cotton' },
            { label: 'Weave Type', value: 'Single Jersey Knit' },
            { label: 'Fabric Weight', value: '180 gsm' },
            { label: 'Collar Style', value: 'Ribbed Crew Neck (1×1 rib)' },
            { label: 'Sleeve Type', value: 'Short Sleeve, Set-in' },
            { label: 'Hem Finish', value: 'Double-needle (5mm)' },
            { label: 'Stitch Count', value: '32 stitches/10cm' },
            { label: 'Shrinkage Rate', value: '≤3% after wash' },
            { label: 'Colorfastness', value: 'Grade 4-5 (ISO 105-C06)' },
            { label: 'Care Instructions', value: 'Machine wash 40°C, tumble dry low' },
        ],
        materials: [
            { sku: 'SKU-001', name: 'Combed Cotton Yarn (20s)', qty: 220, unit: 'grams', category: 'Yarn', warehouse: 'Main Warehouse', cost: 0.85, stockStatus: 'In Stock' },
            { sku: 'SKU-DYE-BLU', name: 'Reactive Dye — Royal Blue', qty: 12, unit: 'grams', category: 'Chemical', warehouse: 'Main Warehouse', cost: 0.22, stockStatus: 'In Stock' },
            { sku: 'SKU-004', name: 'Elastic Rib Band (1×1)', qty: 35, unit: 'cm', category: 'Accessory', warehouse: 'Main Warehouse', cost: 0.04, stockStatus: 'In Stock' },
            { sku: 'SKU-007', name: 'Polyester Sewing Thread (40s)', qty: 180, unit: 'meters', category: 'Accessory', warehouse: 'Main Warehouse', cost: 0.09, stockStatus: 'In Stock' },
            { sku: 'SKU-LBL-001', name: 'Woven Care Label', qty: 1, unit: 'pc', category: 'Label', warehouse: 'South Distribution Hub', cost: 1.50, stockStatus: 'In Stock' },
            { sku: 'SKU-LBL-002', name: 'Brand Neck Label (Monti)', qty: 1, unit: 'pc', category: 'Label', warehouse: 'South Distribution Hub', cost: 2.20, stockStatus: 'In Stock' },
            { sku: 'SKU-PKG-001', name: 'OPP Poly Bag (30×40cm)', qty: 1, unit: 'pc', category: 'Packaging', warehouse: 'South Distribution Hub', cost: 1.80, stockStatus: 'In Stock' },
            { sku: 'SKU-CHM-001', name: 'Softener Finishing Agent', qty: 5, unit: 'ml', category: 'Chemical', warehouse: 'Main Warehouse', cost: 0.12, stockStatus: 'In Stock' },
            { sku: 'SKU-CHM-002', name: 'Fixative (Color Lock)', qty: 3, unit: 'ml', category: 'Chemical', warehouse: 'Main Warehouse', cost: 0.08, stockStatus: 'In Stock' },
        ],
    },
    {
        id: 'PRD-002',
        name: 'Navy Slim-Fit Polo Shirt',
        sku: 'PSH-NVY-SLM-002',
        category: 'Apparel',
        subcategory: 'Polo Shirts',
        status: 'Active',
        colorTag: 'slate',
        colorHex: '#1e3a5f',
        colorName: 'Deep Navy',
        sizes: ['S', 'M', 'L', 'XL', 'XXL'],
        weight: '220 gsm',
        dimensions: '72cm × 52cm (M size)',
        batchSize: 300,
        leadTime: '10 days',
        unitCost: 420.00,
        sellingPrice: 950.00,
        stockOnHand: 680,
        moq: 50,
        certification: 'OEKO-TEX Standard 100',
        description: 'Premium piqué polo shirt with 3-button placket, ribbed collar and cuffs. Side vents for comfort. Suitable for corporate and retail use.',
        specs: [
            { label: 'Fabric Composition', value: '60% Cotton / 40% Polyester' },
            { label: 'Weave Type', value: 'Piqué (Waffle) Knit' },
            { label: 'Fabric Weight', value: '220 gsm' },
            { label: 'Collar Style', value: 'Flat Knit Polo Collar' },
            { label: 'Placket', value: '3-Button Woven Placket' },
            { label: 'Sleeve Type', value: 'Short Sleeve, Set-in' },
            { label: 'Side Vent', value: '8cm split hem vent' },
            { label: 'Stitch Count', value: '28 stitches/10cm' },
            { label: 'Shrinkage Rate', value: '≤4% after wash' },
            { label: 'Colorfastness', value: 'Grade 4 (ISO 105-C06)' },
            { label: 'Care Instructions', value: 'Machine wash 30°C, do not bleach' },
        ],
        materials: [
            { sku: 'SKU-CTN-P', name: 'Combed Cotton Yarn (30s) — Piqué', qty: 155, unit: 'grams', category: 'Yarn', warehouse: 'Main Warehouse', cost: 0.92, stockStatus: 'In Stock' },
            { sku: 'SKU-PLY-P', name: 'Polyester Yarn (50D)', qty: 95, unit: 'grams', category: 'Yarn', warehouse: 'Main Warehouse', cost: 0.65, stockStatus: 'In Stock' },
            { sku: 'SKU-DYE-NVY', name: 'Vat Dye — Deep Navy', qty: 18, unit: 'grams', category: 'Chemical', warehouse: 'Main Warehouse', cost: 0.38, stockStatus: 'In Stock' },
            { sku: 'SKU-BTN-001', name: 'Coconut Shell Button (12L)', qty: 3, unit: 'pcs', category: 'Accessory', warehouse: 'North Storage Facility', cost: 1.20, stockStatus: 'In Stock' },
            { sku: 'SKU-004', name: 'Elastic Rib Band (2×2)', qty: 50, unit: 'cm', category: 'Accessory', warehouse: 'Main Warehouse', cost: 0.06, stockStatus: 'In Stock' },
            { sku: 'SKU-007', name: 'Polyester Sewing Thread (40s)', qty: 220, unit: 'meters', category: 'Accessory', warehouse: 'Main Warehouse', cost: 0.09, stockStatus: 'In Stock' },
            { sku: 'SKU-INT-001', name: 'Woven Interlining (Placket)', qty: 1, unit: 'pc', category: 'Raw Material', warehouse: 'East Textile Depot', cost: 3.50, stockStatus: 'Low Stock' },
            { sku: 'SKU-LBL-001', name: 'Woven Care Label', qty: 1, unit: 'pc', category: 'Label', warehouse: 'South Distribution Hub', cost: 1.50, stockStatus: 'In Stock' },
            { sku: 'SKU-LBL-002', name: 'Brand Neck Label (Monti)', qty: 1, unit: 'pc', category: 'Label', warehouse: 'South Distribution Hub', cost: 2.20, stockStatus: 'In Stock' },
            { sku: 'SKU-PKG-002', name: 'OPP Poly Bag (35×45cm)', qty: 1, unit: 'pc', category: 'Packaging', warehouse: 'South Distribution Hub', cost: 2.10, stockStatus: 'In Stock' },
        ],
    },
    {
        id: 'PRD-003',
        name: 'Premium Denim Jeans (Dark Wash)',
        sku: 'DNM-DRK-SLM-003',
        category: 'Apparel',
        subcategory: 'Bottoms',
        status: 'Active',
        colorTag: 'indigo',
        colorHex: '#312e81',
        colorName: 'Dark Indigo Wash',
        sizes: ['28', '30', '32', '34', '36', '38'],
        weight: '12 oz denim',
        dimensions: 'Inseam: 76cm (32)',
        batchSize: 200,
        leadTime: '14 days',
        unitCost: 820.00,
        sellingPrice: 1850.00,
        stockOnHand: 340,
        moq: 50,
        certification: 'BLUESIGN® Certified',
        description: 'Slim-fit 5-pocket denim jeans in dark indigo wash. Made from 12oz ring-spun denim with 2% elastane for stretch. Enzyme stone-washed and finished with whisker detailing.',
        specs: [
            { label: 'Fabric Composition', value: '98% Cotton / 2% Elastane' },
            { label: 'Weave Type', value: '3×1 Right-Hand Twill (Ring-Spun)' },
            { label: 'Fabric Weight', value: '12 oz/yd²' },
            { label: 'Fit Type', value: 'Slim Fit, Mid-Rise' },
            { label: 'Pocket Style', value: '5-Pocket Construction' },
            { label: 'Waistband', value: 'Belt Loop Waistband (6 loops)' },
            { label: 'Closure', value: 'YKK Zip Fly + Metal Button' },
            { label: 'Wash Type', value: 'Enzyme Stone Wash + Whisker' },
            { label: 'Shrinkage Rate', value: '≤3% after first wash' },
            { label: 'Colorfastness', value: 'Grade 3-4 (ISO 105-C06)' },
            { label: 'Care Instructions', value: 'Machine wash 30°C, wash inside out' },
        ],
        materials: [
            { sku: 'SKU-201', name: 'Denim Fabric — Dark Indigo (12oz)', qty: 1.4, unit: 'meters', category: 'Raw Material', warehouse: 'South Distribution Hub', cost: 145.00, stockStatus: 'In Stock' },
            { sku: 'SKU-ELA-001', name: 'Elastane Yarn (40D)', qty: 28, unit: 'grams', category: 'Yarn', warehouse: 'Main Warehouse', cost: 1.80, stockStatus: 'In Stock' },
            { sku: 'SKU-DYE-IND', name: 'Indigo Vat Dye', qty: 22, unit: 'grams', category: 'Chemical', warehouse: 'Main Warehouse', cost: 0.45, stockStatus: 'In Stock' },
            { sku: 'SKU-CHM-SNW', name: 'Stone-Wash Enzyme (Cellulase)', qty: 15, unit: 'ml', category: 'Chemical', warehouse: 'Main Warehouse', cost: 0.62, stockStatus: 'In Stock' },
            { sku: 'SKU-ZPR-001', name: 'YKK Metal Zip Fly (18cm)', qty: 1, unit: 'pc', category: 'Accessory', warehouse: 'North Storage Facility', cost: 12.50, stockStatus: 'In Stock' },
            { sku: 'SKU-BTN-DNM', name: 'Riveted Shank Button (17mm)', qty: 1, unit: 'pc', category: 'Accessory', warehouse: 'North Storage Facility', cost: 4.80, stockStatus: 'In Stock' },
            { sku: 'SKU-RVT-001', name: 'Copper Rivet (6mm)', qty: 6, unit: 'pcs', category: 'Accessory', warehouse: 'North Storage Facility', cost: 0.80, stockStatus: 'In Stock' },
            { sku: 'SKU-007', name: 'Polyester Sewing Thread (40s)', qty: 380, unit: 'meters', category: 'Accessory', warehouse: 'Main Warehouse', cost: 0.09, stockStatus: 'In Stock' },
            { sku: 'SKU-PCK-DNM', name: 'Pocket Lining Fabric (Twill)', qty: 0.4, unit: 'meters', category: 'Raw Material', warehouse: 'East Textile Depot', cost: 28.00, stockStatus: 'In Stock' },
            { sku: 'SKU-LBL-DNM', name: 'Leather Patch Label', qty: 1, unit: 'pc', category: 'Label', warehouse: 'South Distribution Hub', cost: 8.50, stockStatus: 'Low Stock' },
            { sku: 'SKU-LBL-001', name: 'Woven Care Label', qty: 1, unit: 'pc', category: 'Label', warehouse: 'South Distribution Hub', cost: 1.50, stockStatus: 'In Stock' },
            { sku: 'SKU-PKG-003', name: 'Kraft Hang Tag + String', qty: 1, unit: 'set', category: 'Packaging', warehouse: 'South Distribution Hub', cost: 3.50, stockStatus: 'In Stock' },
        ],
    },
    {
        id: 'PRD-004',
        name: 'Merino Wool Scarf (Herringbone)',
        sku: 'SCF-MRN-HRB-004',
        category: 'Accessories',
        subcategory: 'Scarves',
        status: 'Active',
        colorTag: 'stone',
        colorHex: '#78716c',
        colorName: 'Natural Oatmeal',
        sizes: ['One Size'],
        weight: '320 gsm',
        dimensions: '180cm × 30cm',
        batchSize: 150,
        leadTime: '12 days',
        unitCost: 580.00,
        sellingPrice: 1400.00,
        stockOnHand: 210,
        moq: 30,
        certification: 'Woolmark Certified',
        description: 'Luxury herringbone woven scarf in 100% extra-fine Merino wool. Finished with hand-knotted fringe. Naturally temperature-regulating and odor-resistant.',
        specs: [
            { label: 'Fabric Composition', value: '100% Extra-Fine Merino Wool (18.5μ)' },
            { label: 'Weave Type', value: 'Herringbone Twill (2/2)' },
            { label: 'Fabric Weight', value: '320 gsm' },
            { label: 'Dimensions', value: '180cm × 30cm' },
            { label: 'Fringe Length', value: '5cm hand-knotted fringe both ends' },
            { label: 'Thread Count', value: '48 ends/10cm' },
            { label: 'Felting Rate', value: 'Superwash-treated, anti-felt' },
            { label: 'Pilling Resistance', value: 'Grade 4 (Martindale 5,000 cycles)' },
            { label: 'Colorfastness', value: 'Grade 4-5 (ISO 105-C06)' },
            { label: 'Care Instructions', value: 'Hand wash 30°C or dry clean only' },
        ],
        materials: [
            { sku: 'SKU-WOL-301', name: 'Merino Wool Top (18.5μ)', qty: 95, unit: 'grams', category: 'Yarn', warehouse: 'East Textile Depot', cost: 4.80, stockStatus: 'In Stock' },
            { sku: 'SKU-DYE-NAT', name: 'Acid Dye — Natural Oatmeal', qty: 8, unit: 'grams', category: 'Chemical', warehouse: 'Main Warehouse', cost: 0.55, stockStatus: 'In Stock' },
            { sku: 'SKU-CHM-WOL', name: 'Wool Superwash Agent (Chlorine-Free)', qty: 10, unit: 'ml', category: 'Chemical', warehouse: 'Main Warehouse', cost: 0.90, stockStatus: 'In Stock' },
            { sku: 'SKU-CHM-MOT', name: 'Mothproofing Agent (Eurolan)', qty: 5, unit: 'ml', category: 'Chemical', warehouse: 'Main Warehouse', cost: 0.35, stockStatus: 'In Stock' },
            { sku: 'SKU-007', name: 'Merino Sewing Thread (60s)', qty: 60, unit: 'meters', category: 'Accessory', warehouse: 'Main Warehouse', cost: 0.12, stockStatus: 'In Stock' },
            { sku: 'SKU-LBL-002', name: 'Brand Woven Label (Monti)', qty: 1, unit: 'pc', category: 'Label', warehouse: 'South Distribution Hub', cost: 2.20, stockStatus: 'In Stock' },
            { sku: 'SKU-LBL-WMK', name: 'Woolmark License Tag', qty: 1, unit: 'pc', category: 'Label', warehouse: 'South Distribution Hub', cost: 3.80, stockStatus: 'Low Stock' },
            { sku: 'SKU-PKG-SCF', name: 'Tissue Paper + Gift Box', qty: 1, unit: 'set', category: 'Packaging', warehouse: 'South Distribution Hub', cost: 18.00, stockStatus: 'In Stock' },
        ],
    },
    {
        id: 'PRD-005',
        name: 'Linen Relaxed Blazer (Ivory)',
        sku: 'BLZ-IVR-RLX-005',
        category: 'Apparel',
        subcategory: 'Outerwear',
        status: 'Active',
        colorTag: 'amber',
        colorHex: '#d97706',
        colorName: 'Ivory White',
        sizes: ['XS', 'S', 'M', 'L', 'XL'],
        weight: '200 gsm',
        dimensions: '74cm × 58cm (M size)',
        batchSize: 120,
        leadTime: '18 days',
        unitCost: 1240.00,
        sellingPrice: 3200.00,
        stockOnHand: 95,
        moq: 20,
        certification: 'GOTS Certified',
        description: 'Relaxed-fit single-breasted blazer in GOTS-certified natural linen. Unlined for breathability. Features notched lapel, two flap pockets, and a single back vent.',
        specs: [
            { label: 'Fabric Composition', value: '100% Natural Linen (European Flax)' },
            { label: 'Weave Type', value: 'Plain Weave (2×2)' },
            { label: 'Fabric Weight', value: '200 gsm' },
            { label: 'Fit Type', value: 'Relaxed / Unstructured' },
            { label: 'Lining', value: 'Unlined (full canvas front)' },
            { label: 'Lapel Style', value: 'Notched Lapel' },
            { label: 'Closure', value: '2-Button Single-Breasted' },
            { label: 'Pockets', value: 'Chest welt + 2 flap hip pockets' },
            { label: 'Back Vent', value: 'Single centre vent (18cm)' },
            { label: 'Colorfastness', value: 'Grade 4 (ISO 105-C06)' },
            { label: 'Care Instructions', value: 'Dry clean recommended, steam press' },
        ],
        materials: [
            { sku: 'SKU-006', name: 'Natural Linen Fabric (200gsm)', qty: 2.2, unit: 'meters', category: 'Raw Material', warehouse: 'Main Warehouse', cost: 98.00, stockStatus: 'In Stock' },
            { sku: 'SKU-CVS-001', name: 'Chest Canvas (Hair Interfacing)', qty: 0.4, unit: 'meters', category: 'Raw Material', warehouse: 'East Textile Depot', cost: 35.00, stockStatus: 'In Stock' },
            { sku: 'SKU-INT-LPN', name: 'Woven Fusible Interfacing (Lapel)', qty: 0.3, unit: 'meters', category: 'Raw Material', warehouse: 'East Textile Depot', cost: 22.00, stockStatus: 'Low Stock' },
            { sku: 'SKU-DYE-IVR', name: 'Acid Dye — Ivory White', qty: 6, unit: 'grams', category: 'Chemical', warehouse: 'Main Warehouse', cost: 0.42, stockStatus: 'In Stock' },
            { sku: 'SKU-BTN-LNB', name: 'Natural Shell Button (20L)', qty: 4, unit: 'pcs', category: 'Accessory', warehouse: 'North Storage Facility', cost: 3.80, stockStatus: 'In Stock' },
            { sku: 'SKU-007', name: 'Linen Sewing Thread (80s)', qty: 560, unit: 'meters', category: 'Accessory', warehouse: 'Main Warehouse', cost: 0.11, stockStatus: 'In Stock' },
            { sku: 'SKU-SLK-PAD', name: 'Shoulder Pad (Soft Foam, 1cm)', qty: 2, unit: 'pcs', category: 'Accessory', warehouse: 'East Textile Depot', cost: 12.00, stockStatus: 'In Stock' },
            { sku: 'SKU-SLK-TPS', name: 'Sleeve Head Wadding (Felt)', qty: 2, unit: 'pcs', category: 'Accessory', warehouse: 'East Textile Depot', cost: 4.50, stockStatus: 'In Stock' },
            { sku: 'SKU-LBL-001', name: 'Woven Care Label', qty: 1, unit: 'pc', category: 'Label', warehouse: 'South Distribution Hub', cost: 1.50, stockStatus: 'In Stock' },
            { sku: 'SKU-LBL-002', name: 'Brand Neck Label (Monti)', qty: 1, unit: 'pc', category: 'Label', warehouse: 'South Distribution Hub', cost: 2.20, stockStatus: 'In Stock' },
            { sku: 'SKU-LBL-GOT', name: 'GOTS Certification Hang Tag', qty: 1, unit: 'pc', category: 'Label', warehouse: 'South Distribution Hub', cost: 5.50, stockStatus: 'In Stock' },
            { sku: 'SKU-PKG-004', name: 'Garment Bag (Breathable Non-Woven)', qty: 1, unit: 'pc', category: 'Packaging', warehouse: 'South Distribution Hub', cost: 22.00, stockStatus: 'In Stock' },
        ],
    },
    {
        id: 'PRD-006',
        name: 'Athletic Compression Shorts',
        sku: 'ATH-CMP-SHT-006',
        category: 'Sportswear',
        subcategory: 'Shorts',
        status: 'Active',
        colorTag: 'emerald',
        colorHex: '#059669',
        colorName: 'Forest Green',
        sizes: ['XS', 'S', 'M', 'L', 'XL', 'XXL'],
        weight: '220 gsm',
        dimensions: '42cm length × 36cm waist (M)',
        batchSize: 400,
        leadTime: '8 days',
        unitCost: 310.00,
        sellingPrice: 750.00,
        stockOnHand: 820,
        moq: 100,
        certification: 'bluesign® Approved',
        description: '4-way stretch compression shorts with flatlock seaming and moisture-wicking finish. Features a 4" waistband with internal drawcord and a zippered back pocket.',
        specs: [
            { label: 'Fabric Composition', value: '82% Nylon / 18% Elastane' },
            { label: 'Weave Type', value: 'Circular Knit (4-way stretch)' },
            { label: 'Fabric Weight', value: '220 gsm' },
            { label: 'Seam Construction', value: 'Flatlock (chafe-free)' },
            { label: 'Waistband', value: '10cm wide, internal drawcord' },
            { label: 'Pocket', value: '1× Rear zip pocket (YKK)' },
            { label: 'Moisture Management', value: 'Moisture-wick finish (Coolmax® tech)' },
            { label: 'UV Protection', value: 'UPF 30+' },
            { label: 'Stretch Recovery', value: '≥95% after 20 wash cycles' },
            { label: 'Care Instructions', value: 'Machine wash 30°C, do not tumble dry' },
        ],
        materials: [
            { sku: 'SKU-NYL-220', name: 'Nylon Fabric (4-way stretch, 220gsm)', qty: 0.5, unit: 'meters', category: 'Raw Material', warehouse: 'Main Warehouse', cost: 62.00, stockStatus: 'In Stock' },
            { sku: 'SKU-ELA-001', name: 'Elastane Yarn (40D)', qty: 18, unit: 'grams', category: 'Yarn', warehouse: 'Main Warehouse', cost: 1.80, stockStatus: 'In Stock' },
            { sku: 'SKU-DYE-GRN', name: 'Disperse Dye — Forest Green', qty: 10, unit: 'grams', category: 'Chemical', warehouse: 'Main Warehouse', cost: 0.32, stockStatus: 'In Stock' },
            { sku: 'SKU-CHM-MCK', name: 'Moisture-Wicking Finish Agent', qty: 8, unit: 'ml', category: 'Chemical', warehouse: 'Main Warehouse', cost: 0.75, stockStatus: 'In Stock' },
            { sku: 'SKU-ELS-WBD', name: 'Elastic Waistband (10cm wide)', qty: 76, unit: 'cm', category: 'Accessory', warehouse: 'Main Warehouse', cost: 0.22, stockStatus: 'In Stock' },
            { sku: 'SKU-CRD-001', name: 'Flat Drawcord (8mm, Nylon)', qty: 80, unit: 'cm', category: 'Accessory', warehouse: 'North Storage Facility', cost: 0.14, stockStatus: 'In Stock' },
            { sku: 'SKU-ZPR-ATH', name: 'YKK Zip (10cm, Aquaguard)', qty: 1, unit: 'pc', category: 'Accessory', warehouse: 'North Storage Facility', cost: 8.50, stockStatus: 'In Stock' },
            { sku: 'SKU-007', name: 'Polyester Flatlock Thread (120D)', qty: 250, unit: 'meters', category: 'Accessory', warehouse: 'Main Warehouse', cost: 0.08, stockStatus: 'In Stock' },
            { sku: 'SKU-LBL-ATH', name: 'Silicone Print Label (Heat Transfer)', qty: 1, unit: 'pc', category: 'Label', warehouse: 'South Distribution Hub', cost: 3.20, stockStatus: 'In Stock' },
            { sku: 'SKU-PKG-001', name: 'OPP Poly Bag (30×40cm)', qty: 1, unit: 'pc', category: 'Packaging', warehouse: 'South Distribution Hub', cost: 1.80, stockStatus: 'In Stock' },
        ],
    },
]);

// ── State ─────────────────────────────────────────────────────────────────────
const isLoaded = ref(false);
const searchQuery = ref('');
const catFilter = ref('All');
const statusFilter = ref('All');
const selectedProduct = ref(null);
const activeTab = ref('bom'); // 'bom' | 'specs'
const expandedMat = ref(null);

onMounted(() => setTimeout(() => (isLoaded.value = true), 60));

// ── Computed ──────────────────────────────────────────────────────────────────
const categories = computed(() => ['All', ...new Set(products.value.map(p => p.category))]);

const filtered = computed(() => {
    let list = products.value;
    if (searchQuery.value) {
        const q = searchQuery.value.toLowerCase();
        list = list.filter(p => p.name.toLowerCase().includes(q) || p.sku.toLowerCase().includes(q) || p.id.toLowerCase().includes(q));
    }
    if (catFilter.value !== 'All') list = list.filter(p => p.category === catFilter.value);
    if (statusFilter.value !== 'All') list = list.filter(p => p.status === statusFilter.value);
    return list;
});

const bomCost = (product) => product.materials.reduce((s, m) => s + m.cost * m.qty, 0);

const margin = (product) => {
    const cost = product.unitCost;
    return (((product.sellingPrice - cost) / product.sellingPrice) * 100).toFixed(1);
};

const bomHasAlert = (product) => product.materials.some(m => m.stockStatus !== 'In Stock');

// ── Helpers ───────────────────────────────────────────────────────────────────
const colorDot = {
    blue: 'bg-blue-500',
    slate: 'bg-slate-600',
    indigo: 'bg-indigo-700',
    stone: 'bg-stone-400',
    amber: 'bg-amber-500',
    emerald: 'bg-emerald-500',
};

const matCatColor = {
    'Yarn': 'bg-blue-50 text-blue-700 dark:bg-blue-900/20 dark:text-blue-400',
    'Chemical': 'bg-purple-50 text-purple-700 dark:bg-purple-900/20 dark:text-purple-400',
    'Accessory': 'bg-slate-100 text-slate-600 dark:bg-slate-800 dark:text-slate-300',
    'Raw Material': 'bg-emerald-50 text-emerald-700 dark:bg-emerald-900/20 dark:text-emerald-400',
    'Label': 'bg-amber-50 text-amber-700 dark:bg-amber-900/20 dark:text-amber-400',
    'Packaging': 'bg-rose-50 text-rose-600 dark:bg-rose-900/20 dark:text-rose-400',
};

const stockBadge = (s) => ({
    'In Stock': 'bg-emerald-50 text-emerald-700 ring-1 ring-emerald-200 dark:bg-emerald-900/20 dark:text-emerald-400 dark:ring-emerald-800',
    'Low Stock': 'bg-amber-50 text-amber-700 ring-1 ring-amber-200 dark:bg-amber-900/20 dark:text-amber-400 dark:ring-amber-800',
    'Out of Stock': 'bg-red-50 text-red-600 ring-1 ring-red-200 dark:bg-red-900/20 dark:text-red-400 dark:ring-red-800',
}[s] ?? '');

const fmt = (n) => Number(n).toLocaleString('en-PH', { minimumFractionDigits: 2, maximumFractionDigits: 2 });

const openProduct = (product) => {
    selectedProduct.value = product;
    activeTab.value = 'bom';
    expandedMat.value = null;
};

const closeModal = () => { selectedProduct.value = null; };
</script>

<template>

    <Head title="Product Catalog | Monti Textile" />
    <AuthenticatedLayout>

        <!-- ── Header ─────────────────────────────────────────────────────── -->
        <div class="mb-8 flex flex-col sm:flex-row sm:items-end sm:justify-between gap-4"
            :class="isLoaded ? 'opacity-100 translate-y-0' : 'opacity-0 translate-y-3'"
            style="transition: opacity .45s ease, transform .45s ease;">
            <div>
                <p class="text-[10px] font-black text-blue-600 uppercase tracking-[0.2em] mb-1">Monti Textile ERP</p>
                <h1 class="text-2xl font-black text-slate-900 dark:text-white tracking-tight">Product Catalog</h1>
                <p class="text-slate-500 text-sm mt-0.5">Bill of materials, specifications, and raw material breakdown
                    for every product.</p>
            </div>
            <div class="flex items-center gap-3 text-sm flex-shrink-0">
                <span
                    class="px-3 py-1.5 bg-slate-100 dark:bg-slate-800 rounded-xl font-bold text-slate-600 dark:text-slate-300">
                    {{ products.length }} Products
                </span>
                <span class="px-3 py-1.5 bg-blue-50 dark:bg-blue-900/20 rounded-xl font-bold text-blue-600">
                    {{products.reduce((s, p) => s + p.materials.length, 0)}} Raw Materials
                </span>
            </div>
        </div>

        <!-- ── Filters ────────────────────────────────────────────────────── -->
        <div class="flex flex-wrap gap-3 mb-6 items-center" :class="isLoaded ? 'opacity-100' : 'opacity-0'"
            style="transition: opacity .5s ease .1s;">
            <div class="relative flex-1 min-w-[200px] max-w-xs">
                <Search class="absolute left-3 top-1/2 -translate-y-1/2 w-3.5 h-3.5 text-slate-400" />
                <input v-model="searchQuery" type="text" placeholder="Search product, SKU..."
                    class="pl-9 pr-4 py-2.5 w-full text-sm bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-700 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500/20 text-slate-700 dark:text-slate-200 placeholder-slate-400" />
            </div>
            <div class="relative">
                <select v-model="catFilter"
                    class="appearance-none pl-3 pr-8 py-2.5 text-sm bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-700 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500/20 text-slate-700 dark:text-slate-200 font-semibold">
                    <option v-for="c in categories" :key="c">{{ c }}</option>
                </select>
                <ChevronDown
                    class="absolute right-2.5 top-1/2 -translate-y-1/2 w-3.5 h-3.5 text-slate-400 pointer-events-none" />
            </div>
            <div v-if="searchQuery || catFilter !== 'All'" class="flex items-center gap-1.5">
                <span class="text-xs text-slate-400 font-medium">{{ filtered.length }} results</span>
                <button @click="searchQuery = ''; catFilter = 'All'"
                    class="p-1.5 rounded-lg text-slate-400 hover:text-slate-600 hover:bg-slate-100 dark:hover:bg-slate-800 transition">
                    <X class="w-3.5 h-3.5" />
                </button>
            </div>
        </div>

        <!-- ── Product Grid ───────────────────────────────────────────────── -->
        <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-5">
            <div v-for="(product, i) in filtered" :key="product.id"
                class="group bg-white dark:bg-slate-900 rounded-2xl border border-slate-200 dark:border-slate-800 overflow-hidden hover:shadow-lg hover:border-slate-300 dark:hover:border-slate-700 transition-all duration-300 cursor-pointer flex flex-col"
                :style="`transition-delay: ${i * 50}ms`"
                :class="isLoaded ? 'opacity-100 translate-y-0' : 'opacity-0 translate-y-5'"
                @click="openProduct(product)">

                <!-- Color Banner -->
                <div class="h-2 w-full" :style="`background-color: ${product.colorHex}`" />

                <div class="p-5 flex flex-col gap-4 flex-1">
                    <!-- Top row -->
                    <div class="flex items-start justify-between gap-3">
                        <div class="flex-1 min-w-0">
                            <div class="flex items-center gap-2 mb-1.5">
                                <span
                                    class="font-mono text-[10px] font-bold text-slate-400 bg-slate-100 dark:bg-slate-800 px-2 py-0.5 rounded-md">{{
                                    product.id }}</span>
                                <span
                                    class="text-[10px] font-bold text-slate-400 bg-slate-100 dark:bg-slate-800 px-2 py-0.5 rounded-full">{{
                                    product.subcategory }}</span>
                                <span v-if="bomHasAlert(product)"
                                    class="w-2 h-2 rounded-full bg-amber-500 flex-shrink-0" title="BOM stock alert" />
                            </div>
                            <h3
                                class="font-black text-slate-900 dark:text-white text-base leading-snug group-hover:text-blue-600 dark:group-hover:text-blue-400 transition-colors">
                                {{ product.name }}
                            </h3>
                            <p class="font-mono text-[11px] text-slate-400 mt-0.5">{{ product.sku }}</p>
                        </div>
                        <div class="w-10 h-10 rounded-xl flex-shrink-0 flex items-center justify-center"
                            :style="`background-color: ${product.colorHex}22; border: 1.5px solid ${product.colorHex}44`">
                            <span class="w-3 h-3 rounded-full" :style="`background-color: ${product.colorHex}`" />
                        </div>
                    </div>

                    <!-- Description -->
                    <p class="text-[12px] text-slate-500 dark:text-slate-400 leading-relaxed line-clamp-2">{{
                        product.description }}</p>

                    <!-- Quick Specs -->
                    <div class="grid grid-cols-2 gap-2">
                        <div class="flex items-center gap-2 text-xs text-slate-500">
                            <Weight class="w-3.5 h-3.5 text-slate-300 flex-shrink-0" />
                            <span>{{ product.weight }}</span>
                        </div>
                        <div class="flex items-center gap-2 text-xs text-slate-500">
                            <Ruler class="w-3.5 h-3.5 text-slate-300 flex-shrink-0" />
                            <span class="truncate">{{ product.dimensions }}</span>
                        </div>
                        <div class="flex items-center gap-2 text-xs text-slate-500">
                            <Clock class="w-3.5 h-3.5 text-slate-300 flex-shrink-0" />
                            <span>Lead: {{ product.leadTime }}</span>
                        </div>
                        <div class="flex items-center gap-2 text-xs text-slate-500">
                            <Boxes class="w-3.5 h-3.5 text-slate-300 flex-shrink-0" />
                            <span>{{ product.stockOnHand.toLocaleString() }} on hand</span>
                        </div>
                    </div>

                    <!-- Sizes -->
                    <div class="flex flex-wrap gap-1">
                        <span v-for="sz in product.sizes" :key="sz"
                            class="text-[10px] font-bold px-2 py-0.5 rounded-full bg-slate-100 dark:bg-slate-800 text-slate-500 dark:text-slate-400">
                            {{ sz }}
                        </span>
                    </div>

                    <!-- BOM Summary -->
                    <div
                        class="pt-3 mt-auto border-t border-slate-100 dark:border-slate-800 flex items-center justify-between">
                        <div class="flex items-center gap-3">
                            <div>
                                <p class="text-[10px] text-slate-400 font-bold uppercase tracking-wider">BOM Items</p>
                                <p class="text-sm font-black text-slate-800 dark:text-white">{{ product.materials.length
                                    }} materials</p>
                            </div>
                            <div>
                                <p class="text-[10px] text-slate-400 font-bold uppercase tracking-wider">Margin</p>
                                <p class="text-sm font-black text-emerald-600">{{ margin(product) }}%</p>
                            </div>
                        </div>
                        <div class="flex items-center gap-2">
                            <div class="text-right">
                                <p class="text-[10px] text-slate-400 font-bold">Selling Price</p>
                                <p class="text-sm font-black text-slate-900 dark:text-white">₱{{
                                    fmt(product.sellingPrice) }}</p>
                            </div>
                            <div
                                class="w-8 h-8 rounded-xl bg-slate-100 dark:bg-slate-800 flex items-center justify-center group-hover:bg-blue-600 group-hover:text-white transition-all">
                                <ChevronRight class="w-4 h-4 text-slate-400 group-hover:text-white" />
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Empty state -->
            <div v-if="filtered.length === 0"
                class="col-span-full flex flex-col items-center justify-center py-24 text-slate-400">
                <Package class="w-12 h-12 mb-4 opacity-30" />
                <p class="font-bold text-slate-500">No products match your filters.</p>
                <button @click="searchQuery = ''; catFilter = 'All'"
                    class="mt-3 text-sm text-blue-600 font-bold hover:underline">Clear filters</button>
            </div>
        </div>

        <!-- ── Detail Modal ───────────────────────────────────────────────── -->
        <Teleport to="body">
            <div v-if="selectedProduct"
                class="fixed inset-0 z-50 flex items-start justify-center p-4 pt-8 bg-black/50 backdrop-blur-sm overflow-y-auto"
                @click.self="closeModal">

                <div
                    class="bg-white dark:bg-slate-900 rounded-2xl shadow-2xl border border-slate-200 dark:border-slate-700 w-full max-w-4xl mb-8">

                    <!-- Modal Header -->
                    <div class="h-1.5 w-full rounded-t-2xl" :style="`background-color: ${selectedProduct.colorHex}`" />

                    <div
                        class="px-6 py-5 border-b border-slate-100 dark:border-slate-800 flex items-start justify-between gap-4">
                        <div class="flex-1 min-w-0">
                            <div class="flex items-center gap-2 mb-1.5 flex-wrap">
                                <span
                                    class="font-mono text-[10px] font-bold bg-slate-100 dark:bg-slate-800 text-slate-500 px-2 py-0.5 rounded-md">{{
                                    selectedProduct.id }}</span>
                                <span
                                    class="text-[10px] font-bold bg-slate-100 dark:bg-slate-800 text-slate-500 px-2 py-0.5 rounded-full">{{
                                    selectedProduct.category }} · {{ selectedProduct.subcategory }}</span>
                                <span
                                    class="text-[10px] font-black bg-emerald-50 dark:bg-emerald-900/20 text-emerald-700 dark:text-emerald-400 px-2 py-0.5 rounded-full ring-1 ring-emerald-200 dark:ring-emerald-800">{{
                                    selectedProduct.status }}</span>
                                <span v-if="selectedProduct.certification"
                                    class="text-[10px] font-bold bg-blue-50 dark:bg-blue-900/20 text-blue-600 px-2 py-0.5 rounded-full ring-1 ring-blue-200 dark:ring-blue-800">
                                    ✓ {{ selectedProduct.certification }}
                                </span>
                            </div>
                            <h2 class="text-xl font-black text-slate-900 dark:text-white leading-tight">{{
                                selectedProduct.name }}</h2>
                            <p class="font-mono text-xs text-slate-400 mt-0.5">{{ selectedProduct.sku }}</p>
                        </div>
                        <button @click="closeModal"
                            class="p-2 rounded-xl text-slate-400 hover:text-slate-600 hover:bg-slate-100 dark:hover:bg-slate-800 transition flex-shrink-0">
                            <X class="w-5 h-5" />
                        </button>
                    </div>

                    <!-- Stat Strip -->
                    <div
                        class="grid grid-cols-2 sm:grid-cols-4 divide-x divide-y sm:divide-y-0 divide-slate-100 dark:divide-slate-800 border-b border-slate-100 dark:border-slate-800">
                        <div class="px-5 py-4">
                            <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Unit Cost</p>
                            <p class="text-lg font-black text-slate-900 dark:text-white mt-0.5">₱{{
                                fmt(selectedProduct.unitCost) }}</p>
                        </div>
                        <div class="px-5 py-4">
                            <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Selling Price</p>
                            <p class="text-lg font-black text-emerald-600 mt-0.5">₱{{ fmt(selectedProduct.sellingPrice)
                                }}</p>
                        </div>
                        <div class="px-5 py-4">
                            <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Gross Margin</p>
                            <p class="text-lg font-black text-blue-600 mt-0.5">{{ margin(selectedProduct) }}%</p>
                        </div>
                        <div class="px-5 py-4">
                            <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Stock On Hand</p>
                            <p class="text-lg font-black text-slate-900 dark:text-white mt-0.5">{{
                                selectedProduct.stockOnHand.toLocaleString() }}</p>
                        </div>
                    </div>

                    <!-- Description + Quick Info -->
                    <div
                        class="px-6 py-4 border-b border-slate-100 dark:border-slate-800 grid grid-cols-1 sm:grid-cols-3 gap-5">
                        <div class="sm:col-span-2">
                            <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-1.5">
                                Description</p>
                            <p class="text-sm text-slate-600 dark:text-slate-400 leading-relaxed">{{
                                selectedProduct.description }}</p>
                        </div>
                        <div class="space-y-2.5">
                            <div class="flex items-center gap-2.5">
                                <Palette class="w-3.5 h-3.5 text-slate-400 flex-shrink-0" />
                                <div>
                                    <p class="text-[10px] text-slate-400 font-bold">Colorway</p>
                                    <div class="flex items-center gap-1.5 mt-0.5">
                                        <span class="w-3.5 h-3.5 rounded-full flex-shrink-0"
                                            :style="`background-color: ${selectedProduct.colorHex}`" />
                                        <p class="text-xs font-bold text-slate-700 dark:text-slate-300">{{
                                            selectedProduct.colorName }}</p>
                                    </div>
                                </div>
                            </div>
                            <div class="flex items-center gap-2.5">
                                <Ruler class="w-3.5 h-3.5 text-slate-400 flex-shrink-0" />
                                <div>
                                    <p class="text-[10px] text-slate-400 font-bold">Dimensions</p>
                                    <p class="text-xs font-bold text-slate-700 dark:text-slate-300 mt-0.5">{{
                                        selectedProduct.dimensions }}</p>
                                </div>
                            </div>
                            <div class="flex items-center gap-2.5">
                                <Tag class="w-3.5 h-3.5 text-slate-400 flex-shrink-0" />
                                <div>
                                    <p class="text-[10px] text-slate-400 font-bold">Available Sizes</p>
                                    <div class="flex flex-wrap gap-1 mt-0.5">
                                        <span v-for="sz in selectedProduct.sizes" :key="sz"
                                            class="text-[10px] font-bold px-1.5 py-0.5 rounded bg-slate-100 dark:bg-slate-800 text-slate-500">{{
                                            sz }}</span>
                                    </div>
                                </div>
                            </div>
                            <div class="flex items-center gap-2.5">
                                <Zap class="w-3.5 h-3.5 text-slate-400 flex-shrink-0" />
                                <div>
                                    <p class="text-[10px] text-slate-400 font-bold">Lead Time / MOQ</p>
                                    <p class="text-xs font-bold text-slate-700 dark:text-slate-300 mt-0.5">{{
                                        selectedProduct.leadTime }} · MOQ {{ selectedProduct.moq }} pcs</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Tabs -->
                    <div class="flex border-b border-slate-100 dark:border-slate-800 px-6">
                        <button
                            v-for="tab in [{ id: 'bom', label: 'Bill of Materials', icon: Layers }, { id: 'specs', label: 'Technical Specs', icon: Info }]"
                            :key="tab.id" @click="activeTab = tab.id" :class="[
                                'flex items-center gap-2 px-1 py-3.5 mr-6 text-sm font-bold border-b-2 transition-colors',
                                activeTab === tab.id
                                    ? 'border-blue-600 text-blue-600'
                                    : 'border-transparent text-slate-500 hover:text-slate-700 dark:hover:text-slate-300'
                            ]">
                            <component :is="tab.icon" class="w-3.5 h-3.5" />
                            {{ tab.label }}
                            <span v-if="tab.id === 'bom'" :class="[
                                'text-[10px] font-black px-1.5 py-0.5 rounded-full',
                                activeTab === 'bom' ? 'bg-blue-100 text-blue-600 dark:bg-blue-900/30' : 'bg-slate-100 dark:bg-slate-800 text-slate-400'
                            ]">{{ selectedProduct.materials.length }}</span>
                        </button>
                    </div>

                    <!-- Tab: BOM ─────────────────────────────────────────── -->
                    <div v-if="activeTab === 'bom'" class="p-5">
                        <!-- BOM Header -->
                        <div class="flex items-center justify-between mb-4">
                            <div>
                                <p class="text-xs text-slate-500">Total BOM Material Cost</p>
                                <p class="text-2xl font-black text-slate-900 dark:text-white">₱{{
                                    fmt(bomCost(selectedProduct)) }}</p>
                            </div>
                            <div v-if="bomHasAlert(selectedProduct)"
                                class="flex items-center gap-2 px-3 py-2 bg-amber-50 dark:bg-amber-900/20 rounded-xl border border-amber-200 dark:border-amber-800">
                                <AlertTriangle class="w-4 h-4 text-amber-500" />
                                <p class="text-xs font-bold text-amber-700 dark:text-amber-400">Some materials are
                                    low/out of stock</p>
                            </div>
                        </div>

                        <!-- Category filter chips -->
                        <div class="flex flex-wrap gap-2 mb-4">
                            <span v-for="(_, cat) in matCatColor" :key="cat"
                                :class="['text-[10px] font-black px-2.5 py-1 rounded-full cursor-default', matCatColor[cat]]">
                                {{ cat }}
                                <span class="ml-1 opacity-70">{{selectedProduct.materials.filter(m => m.category ===
                                    cat).length }}</span>
                            </span>
                        </div>

                        <!-- Materials list -->
                        <div class="space-y-2">
                            <div v-for="mat in selectedProduct.materials" :key="mat.sku"
                                class="border border-slate-100 dark:border-slate-800 rounded-xl overflow-hidden hover:border-slate-200 dark:hover:border-slate-700 transition-colors">
                                <button
                                    class="w-full flex items-center gap-4 px-4 py-3.5 text-left hover:bg-slate-50/60 dark:hover:bg-slate-800/40 transition-colors"
                                    @click="expandedMat = expandedMat === mat.sku ? null : mat.sku">
                                    <!-- Status dot -->
                                    <span :class="[
                                        'w-2 h-2 rounded-full flex-shrink-0',
                                        mat.stockStatus === 'In Stock' ? 'bg-emerald-500' : mat.stockStatus === 'Low Stock' ? 'bg-amber-500' : 'bg-red-500'
                                    ]" />
                                    <!-- SKU -->
                                    <span
                                        class="font-mono text-[10px] font-bold text-slate-400 bg-slate-100 dark:bg-slate-800 px-2 py-0.5 rounded w-24 flex-shrink-0 truncate">{{
                                        mat.sku }}</span>
                                    <!-- Name -->
                                    <span
                                        class="flex-1 text-sm font-semibold text-slate-800 dark:text-slate-200 min-w-0 truncate">{{
                                        mat.name }}</span>
                                    <!-- Category badge -->
                                    <span
                                        :class="['text-[10px] font-bold px-2 py-0.5 rounded-full flex-shrink-0 hidden sm:block', matCatColor[mat.category] ?? 'bg-slate-100 text-slate-500']">
                                        {{ mat.category }}
                                    </span>
                                    <!-- Qty + Unit -->
                                    <span
                                        class="text-sm font-black text-slate-700 dark:text-slate-300 flex-shrink-0 w-24 text-right">
                                        {{ mat.qty }} {{ mat.unit }}
                                    </span>
                                    <!-- Line cost -->
                                    <span
                                        class="text-sm font-black text-slate-900 dark:text-white flex-shrink-0 w-24 text-right">
                                        ₱{{ fmt(mat.cost * mat.qty) }}
                                    </span>
                                    <ChevronDown
                                        :class="['w-3.5 h-3.5 text-slate-400 flex-shrink-0 transition-transform', expandedMat === mat.sku ? 'rotate-180' : '']" />
                                </button>
                                <!-- Expanded Detail -->
                                <div v-if="expandedMat === mat.sku"
                                    class="px-4 pb-4 pt-1 bg-slate-50/50 dark:bg-slate-800/30 border-t border-slate-100 dark:border-slate-800 grid grid-cols-2 sm:grid-cols-4 gap-3 text-xs">
                                    <div>
                                        <p class="text-[10px] text-slate-400 font-bold uppercase tracking-wider">Unit
                                            Cost</p>
                                        <p class="font-bold text-slate-700 dark:text-slate-300 mt-0.5">₱{{ fmt(mat.cost)
                                            }} / {{ mat.unit }}</p>
                                    </div>
                                    <div>
                                        <p class="text-[10px] text-slate-400 font-bold uppercase tracking-wider">Qty
                                            Required</p>
                                        <p class="font-bold text-slate-700 dark:text-slate-300 mt-0.5">{{ mat.qty }} {{
                                            mat.unit }}</p>
                                    </div>
                                    <div>
                                        <p class="text-[10px] text-slate-400 font-bold uppercase tracking-wider">
                                            Warehouse</p>
                                        <p class="font-bold text-slate-700 dark:text-slate-300 mt-0.5">{{ mat.warehouse
                                            }}</p>
                                    </div>
                                    <div>
                                        <p class="text-[10px] text-slate-400 font-bold uppercase tracking-wider">Stock
                                            Status</p>
                                        <span
                                            :class="['inline-block mt-0.5 text-[10px] font-black px-2 py-0.5 rounded-full', stockBadge(mat.stockStatus)]">
                                            {{ mat.stockStatus }}
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Tab: Technical Specs ─────────────────────────────── -->
                    <div v-else-if="activeTab === 'specs'" class="p-5">
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                            <div v-for="spec in selectedProduct.specs" :key="spec.label"
                                class="flex items-start gap-3 px-4 py-3 bg-slate-50 dark:bg-slate-800/50 rounded-xl border border-slate-100 dark:border-slate-800">
                                <div class="flex-1 min-w-0">
                                    <p class="text-[10px] font-black text-slate-400 uppercase tracking-wider">{{
                                        spec.label }}</p>
                                    <p class="text-sm font-bold text-slate-800 dark:text-slate-200 mt-0.5">{{ spec.value
                                        }}</p>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </Teleport>

    </AuthenticatedLayout>
</template>

<style scoped>
.line-clamp-2 {
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
}
</style>