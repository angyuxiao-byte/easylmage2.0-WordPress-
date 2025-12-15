# WordPress EasyImages2.0 图床插件
## 插件简介
这是一个专为WordPress设计的图床插件，能够自动将上传到WordPress媒体库的图片同步至[EasyImages2.0](https://github.com/icret/EasyImages2.0)图床服务，并自动替换文章中的图片链接为图床外链。
## 需要定制插件，功能修复，网站开发，页面修改，服务器配置等服务，请联系TG：https://t.me/xxangyu2
## 核心功能
- ✅ **自动同步**：图片上传到WordPress媒体库后自动同步至EasyImages2.0图床
- ✅ **URL替换**：自动将文章中的图片链接替换为图床外链
- ✅ **本地清理**：上传成功后自动删除本地服务器上的图片文件（包括缩略图），节省存储空间
- ✅ **Gutenberg兼容**：完美支持WordPress区块编辑器
- ✅ **后台设置**：提供简洁的后台设置页面，显示图床信息
- ✅ **错误日志**：详细的错误日志记录，便于调试和问题排查
## 安装方法
### 方法一：直接上传
1. 下载插件文件夹 `easylmage2.0-WordPress`
2. 登录WordPress后台
3. 进入 `插件` > `安装插件` > `上传插件`
4. 选择下载的插件文件，点击 `现在安装`
5. 安装完成后点击 `启用插件`
## 配置说明
1. 插件安装并启用后，会自动使用预设的API配置
2. 把文件的当前配置：
   - API地址：`https://换成你的图床网址/api/index.php`
   - API Token：`换成你的token`
3. 如需修改API配置，需要编辑插件文件 `easylmage2.0-WordPress.php` 中的常量定义：
   ```php
   define('SCV_CDN_API_URL', '您的API地址');
   define('SCV_CDN_API_TOKEN', '您的API Token');
### 重要提示
- 请不要轻易禁用或卸载本插件，否则会导致之前通过本插件上传的所有图片在网站上无法显示（链接失效）
- 图片文件本身仍然安全地存储在图床服务器上，并不会丢失
- 图片格式转换由图床服务器自动处理，无需在此设置
## 支持的编辑器
- ✅ 经典编辑器
- ✅ Gutenberg编辑器（区块编辑器）
## 常见问题
### Q: 上传图片失败怎么办？
A: 请检查：
1. API地址和Token是否正确
2. 服务器是否支持cURL
3. 服务器是否允许外部API请求
4. 查看WordPress错误日志获取详细信息
### Q: 图片上传成功但文章中显示破裂怎么办？
A: 请检查：
1. 图床URL是否可访问
2. 插件是否正常启用
3. 图床服务器是否正常运行
### Q: 可以自定义图床API吗？
A: 可以，需要编辑插件文件 `easylmage2.0-WordPress.php` 中的常量定义
### Q: 插件支持哪些图片格式？
A: 支持所有WordPress支持的图片格式，包括：
- JPEG (.jpg, .jpeg)
- PNG (.png)
- GIF (.gif)
- WebP (.webp)
- 等其他图片格式
## 插件代码结构
```
easylmage2.0-WordPress/
├── easylmage2.0-WordPress.php      # 插件主文件
└── README.md         # 插件说明文档
```
### 核心函数
- `scdn_handle_upload`：核心上传处理函数，负责将图片上传到图床
- `scdn_get_attachment_url`：过滤附件URL，返回图床URL
- `scdn_get_attachment_image_src`：过滤图片元素URL，确保使用图床URL
- `scdn_prepare_attachment_for_js`：兼容Gutenberg编辑器，确保返回正确的URL
## 注意事项
1. 请确保您的服务器支持cURL扩展
2. 请确保您的服务器允许外部API请求
3. 建议定期备份图床数据，以防数据丢失
4. 请遵守图床服务的使用条款和规定
## 联系方式
如有问题或建议，欢迎通过以下方式联系：
-小昂裕的百宝库：https://xiaoangyu.cc
- GitHub：[https://github.com/angyuxiao-byte/easylmage2.0-WordPress-]
- 图床演示地址：[https://png.cm/](https://png.cm/)
## 致谢
- 感谢 [EasyImages2.0](https://github.com/icret/EasyImages2.0) 提供的图床服务
- 感谢 WordPress 社区的支持和贡献
**使用愉快！** 
