<?php

if (!function_exists('uploadFile')) {
    function uploadFile($file, $path = 'frontend/upload/files')
    {
        if ($file) {
            $fileName = hexdec(uniqid()) . '.' . $file->extension();
            $file->move(public_path($path), $fileName);
            $filePath = $path . '/' . $fileName;
            return str_replace('\\', '/', $filePath);
        }
        return null;
    }
}
if (! function_exists('setting')) :
    function setting($name, $default = null, $callback = null)
    {
        static $settings;
        if (! $settings) {
            $settings = \App\Models\Setting::get()->pluck('value', 'name')->toArray();
        }

        $return = $settings[$name] ?? $default;

        if (is_callable($callback)) {
            return call_user_func($callback, $return, $settings);
        }

        return $return;
    }
endif;

    public function settingStore(Request $request)
    {
        $data = $request->all();
        unset($data['_token']);

        foreach ($data as $key => $value) {
            if ($request->hasFile($key)) {
                $value = uploadFile($request->file($key));
            }
            $this->optionAdd($key, $value);
        }

        session()->flash('success', 'Data updated successfully!');
        return back();
    }



    protected function optionAdd($key, $value)
    {
        $option = Setting::where('name', $key)->get();
        $option = $option[0] ?? null;
        if ($option) {
            $id = $option->id;
            $exists_value = $option->value;
            if ($exists_value != $value) {
                $this->optionUpdate($id, $value);
            }
        } else {
            Setting::create([
                'name' => $key,
                'value' => $value
            ]);
        }
    }
    protected function optionUpdate($id, $value)
    {
        $setting = Setting::find($id);

        if ($setting) {
            $oldValue = $setting->value;
            if ($oldValue && file_exists(public_path($oldValue))) {
                unlink(public_path($oldValue));
            }

            $setting->update(['value' => $value]);
        }

    }