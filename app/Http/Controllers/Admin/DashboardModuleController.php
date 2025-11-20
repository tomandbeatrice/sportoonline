<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\DashboardModuleService;
use Illuminate\Http\Request;

class DashboardModuleController extends Controller
{
    public function __construct(
        protected DashboardModuleService $moduleService
    ) {}

    /**
     * Get all active modules for a user type
     */
    public function index(Request $request)
    {
        $userType = $request->input('user_type', 'admin');
        $modules = $this->moduleService->getActiveModules($userType);

        return response()->json([
            'modules' => $modules,
        ]);
    }

    /**
     * Get data for a specific module
     */
    public function getData(Request $request, string $moduleName)
    {
        $filters = $request->all();
        $data = $this->moduleService->getModuleData($moduleName, $filters);

        return response()->json([
            'module' => $moduleName,
            'data' => $data,
            'timestamp' => now()->toIso8601String(),
        ]);
    }

    /**
     * Update module configuration
     */
    public function updateConfig(Request $request, int $moduleId)
    {
        $config = $request->validate([
            'config' => 'required|array',
        ]);

        $this->moduleService->updateModuleConfig($moduleId, $config['config']);

        return response()->json([
            'message' => 'Modül yapılandırması güncellendi',
        ]);
    }

    /**
     * Get all module data at once (for dashboard initialization)
     */
    public function getAllData(Request $request)
    {
        $userType = $request->input('user_type', 'admin');
        $modules = $this->moduleService->getActiveModules($userType);

        $data = [];
        foreach ($modules as $module) {
            $data[$module['name']] = $this->moduleService->getModuleData(
                $module['name'],
                $module['config'] ?? []
            );
        }

        return response()->json([
            'modules' => $modules,
            'data' => $data,
            'timestamp' => now()->toIso8601String(),
        ]);
    }
}
